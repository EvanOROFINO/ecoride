<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Avis;
use App\Models\Covoiturage;
use App\Models\Participation;
use App\Models\Preference;
use App\Models\User;
use App\Models\Voiture;

final class CovoiturageController extends Controller
{
    /**
     * US 3 : Liste/recherche des covoiturages.
     * US 4 : Filtres (écologique, prix max, durée max, note min).
     */
    public function index(): void
    {
        $depart  = $this->input('depart');
        $arrivee = $this->input('arrivee');
        $date    = $this->input('date');

        // Filtres US 4
        $ecoOnly  = isset($_GET['eco']);
        $prixMax  = isset($_GET['prix_max'])   && $_GET['prix_max']   !== '' ? (float) $_GET['prix_max']   : null;
        $dureeMax = isset($_GET['duree_max'])  && $_GET['duree_max']  !== '' ? (int)   $_GET['duree_max']  : null;
        $noteMin  = isset($_GET['note_min'])   && $_GET['note_min']   !== '' ? (float) $_GET['note_min']   : null;

        $covoiturages = [];
        $prochaineDate = null;
        $hasSearch = $depart !== null && $arrivee !== null && $date !== null && $depart !== '' && $arrivee !== '' && $date !== '';

        if ($hasSearch) {
            $covoiturages = Covoiturage::search($depart, $arrivee, $date);

            // Application des filtres en mémoire (US 4)
            $covoiturages = array_filter($covoiturages, function ($c) use ($ecoOnly, $prixMax, $dureeMax, $noteMin) {
                if ($ecoOnly && $c['energie'] !== 'electrique') return false;
                if ($prixMax !== null && (float) $c['prix_personne'] > $prixMax) return false;
                if ($dureeMax !== null && (int) $c['duree_minutes'] > $dureeMax) return false;
                if ($noteMin !== null) {
                    $note = $c['chauffeur_note'] !== null ? (float) $c['chauffeur_note'] : 0;
                    if ($note < $noteMin) return false;
                }
                return true;
            });
            $covoiturages = array_values($covoiturages);

            // Si aucun résultat, chercher la prochaine date dispo
            if (empty($covoiturages)) {
                $prochaineDate = Covoiturage::findNextAvailableDate($depart, $arrivee, $date);
            }
        }

        $this->view('covoiturage/index', [
            'pageTitle'     => $hasSearch ? "Trajets $depart → $arrivee" : 'Recherche de covoiturage',
            'depart'        => $depart,
            'arrivee'       => $arrivee,
            'date'          => $date ?? date('Y-m-d'),
            'hasSearch'     => $hasSearch,
            'covoiturages'  => $covoiturages,
            'prochaineDate' => $prochaineDate,
            'filters' => [
                'eco'       => $ecoOnly,
                'prix_max'  => $prixMax,
                'duree_max' => $dureeMax,
                'note_min'  => $noteMin,
            ],
        ]);
    }

    /**
     * US 5 : Détail d'un covoiturage (avis chauffeur, véhicule, préférences).
     */
    public function show(string $id): void
    {
        $covoiturage = Covoiturage::findById((int) $id);
        if (!$covoiturage) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $avis = Avis::findValidesForChauffeur((int) $covoiturage['chauffeur_id']);
        $preferences = Preference::findByUser((int) $covoiturage['chauffeur_id']);
        $noteMoyenne = User::getAverageRating((int) $covoiturage['chauffeur_id']);

        // L'utilisateur peut-il participer ?
        $canParticipate = false;
        $reason = '';
        if (Auth::check()) {
            if ((int) $covoiturage['chauffeur_id'] === Auth::id()) {
                $reason = 'Vous êtes le chauffeur de ce trajet.';
            } elseif (Participation::exists((int) $id, Auth::id())) {
                $reason = 'Vous êtes déjà inscrit à ce trajet.';
            } elseif ((int) $covoiturage['places_restantes'] < 1) {
                $reason = 'Plus de place disponible.';
            } elseif ((Auth::user()['credit'] ?? 0) < (float) $covoiturage['prix_personne']) {
                $reason = 'Crédit insuffisant pour participer (' . $covoiturage['prix_personne'] . ' crédits requis).';
            } else {
                $canParticipate = true;
            }
        }

        $this->view('covoiturage/show', [
            'pageTitle'      => "Trajet {$covoiturage['lieu_depart']} → {$covoiturage['lieu_arrivee']}",
            'covoiturage'    => $covoiturage,
            'avis'           => $avis,
            'preferences'    => $preferences,
            'noteMoyenne'    => $noteMoyenne,
            'canParticipate' => $canParticipate,
            'reason'         => $reason,
        ]);
    }

    /**
     * US 6 : Participer à un covoiturage.
     * Transaction : décrément crédit passager + décrément place + création participation + crédit chauffeur en attente.
     */
    public function participer(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $covoiturage = Covoiturage::findById((int) $id);
        if (!$covoiturage) {
            $this->flash('error', 'Covoiturage introuvable.');
            $this->redirect('/covoiturages');
        }

        if ((int) $covoiturage['chauffeur_id'] === Auth::id()) {
            $this->flash('error', 'Vous ne pouvez pas participer à votre propre trajet.');
            $this->redirect("/covoiturages/$id");
        }

        if (Participation::exists((int) $id, Auth::id())) {
            $this->flash('error', 'Vous êtes déjà inscrit à ce trajet.');
            $this->redirect("/covoiturages/$id");
        }

        if ((int) $covoiturage['places_restantes'] < 1) {
            $this->flash('error', 'Plus de place disponible.');
            $this->redirect("/covoiturages/$id");
        }

        $prix = (int) $covoiturage['prix_personne'];
        $userCredit = (int) (Auth::user()['credit'] ?? 0);
        if ($userCredit < $prix) {
            $this->flash('error', 'Crédit insuffisant.');
            $this->redirect("/covoiturages/$id");
        }

        // Transaction : on prélève le crédit + on crée la participation
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            // Vérification atomique des places (évite les race conditions)
            $stmt = $db->prepare(
                'SELECT (c.nb_place - (SELECT COUNT(*) FROM participation p WHERE p.covoiturage_id = c.covoiturage_id AND p.statut_validation != "annule")) AS places
                 FROM covoiturage c WHERE c.covoiturage_id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $id]);
            $places = (int) $stmt->fetchColumn();
            if ($places < 1) {
                throw new \RuntimeException('Plus de place disponible.');
            }

            User::updateCredit(Auth::id(), -$prix);
            Participation::create((int) $id, Auth::id());

            $db->commit();
            \App\Core\Auth::setCredit($userCredit - $prix);
            $this->flash('success', 'Inscription confirmée ! ' . $prix . ' crédits ont été débités.');
        } catch (\Throwable $e) {
            $db->rollBack();
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect("/covoiturages/$id");
    }
}
