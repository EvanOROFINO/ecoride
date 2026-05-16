<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Repositories\AvisRepository;
use App\Repositories\CovoiturageRepository;
use App\Repositories\ParticipationRepository;
use App\Repositories\PreferenceRepository;
use App\Repositories\UserRepository;
use App\Services\CovoiturageService;
use DomainException;

final class CovoiturageController extends Controller
{
    private CovoiturageService $service;

    public function __construct()
    {
        $this->service = new CovoiturageService();
    }

    /**
     * Endpoint JSON pour la recherche live (utilisé par public/js/search-live.js via fetch).
     * Renvoie max 20 résultats au format JSON.
     */
    public function apiSearch(): void
    {
        $depart  = trim((string) ($_GET['depart']  ?? ''));
        $arrivee = trim((string) ($_GET['arrivee'] ?? ''));
        $date    = (string) ($_GET['date']    ?? '');
        $limit   = max(1, min(20, (int) ($_GET['limit'] ?? 10)));

        if ($depart === '' || $arrivee === '' || $date === '') {
            $this->json(['error' => 'Paramètres depart, arrivee et date requis.'], 400);
        }

        $rows = CovoiturageRepository::search($depart, $arrivee, $date);
        $results = array_slice(array_map(fn($r) => [
            'id'               => (int) $r['covoiturage_id'],
            'chauffeur_pseudo' => $r['chauffeur_pseudo'],
            'heure_depart'    => substr((string) $r['heure_depart'], 0, 5),
            'heure_arrivee'   => substr((string) $r['heure_arrivee'], 0, 5),
            'prix_personne'   => (float) $r['prix_personne'],
            'places_restantes'=> (int) $r['places_restantes'],
            'energie'         => $r['energie'],
        ], $rows), 0, $limit);

        $this->json([
            'total'   => count($rows),
            'depart'  => $depart,
            'arrivee' => $arrivee,
            'date'    => $date,
            'results' => $results,
        ]);
    }

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
            $covoiturages = CovoiturageRepository::search($depart, $arrivee, $date);

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
                $prochaineDate = CovoiturageRepository::findNextAvailableDate($depart, $arrivee, $date);
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
        $covoiturage = CovoiturageRepository::findById((int) $id);
        if (!$covoiturage) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $avis = AvisRepository::findValidesForChauffeur((int) $covoiturage['chauffeur_id']);
        $preferences = PreferenceRepository::findByUser((int) $covoiturage['chauffeur_id']);
        $noteMoyenne = UserRepository::getAverageRating((int) $covoiturage['chauffeur_id']);

        // L'utilisateur peut-il participer ?
        $canParticipate = false;
        $reason = '';
        if (Auth::check()) {
            if ((int) $covoiturage['chauffeur_id'] === Auth::id()) {
                $reason = 'Vous êtes le chauffeur de ce trajet.';
            } elseif (ParticipationRepository::exists((int) $id, Auth::id())) {
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
     * Logique métier (vérifications + transaction) déléguée à CovoiturageService.
     */
    public function participer(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $userCredit = (int) (Auth::user()['credit'] ?? 0);

        try {
            $prix = $this->service->participer((int) $id, (int) Auth::id(), $userCredit);
            Auth::setCredit($userCredit - $prix);
            $this->flash('success', 'Inscription confirmée ! ' . $prix . ' crédits ont été débités.');
        } catch (DomainException $e) {
            $this->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect("/covoiturages/$id");
    }
}
