<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Repositories\AvisRepository;
use App\Repositories\CovoiturageRepository;
use App\Repositories\MarqueRepository;
use App\Repositories\ParticipationRepository;
use App\Repositories\PreferenceRepository;
use App\Repositories\UserRepository;
use App\Repositories\VoitureRepository;

final class UserController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        $user = UserRepository::findById($userId);

        $voyages = CovoiturageRepository::findByPassager($userId);
        $voyagesChauffeur = in_array('chauffeur', $user['roles'] ?? [], true)
            ? CovoiturageRepository::findByChauffeur($userId)
            : [];

        $this->view('user/dashboard', [
            'pageTitle'       => 'Mon espace',
            'user'            => $user,
            'voyages'         => $voyages,
            'voyagesChauffeur'=> $voyagesChauffeur,
        ]);
    }

    /**
     * US 8 : Sélection des rôles chauffeur/passager.
     */
    public function updateRole(): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $isChauffeur = isset($_POST['role_chauffeur']);
        $isPassager  = isset($_POST['role_passager']);

        $roles = ['utilisateur'];
        if ($isChauffeur) $roles[] = 'chauffeur';
        if ($isPassager)  $roles[] = 'passager';

        UserRepository::setRoles(Auth::id(), $roles);

        // Mise à jour des rôles en session
        $user = UserRepository::findById(Auth::id());
        Auth::login($user);

        $this->flash('success', 'Vos rôles ont été mis à jour.');
        $this->redirect('/mon-espace');
    }

    /**
     * US 8 : Liste et ajout des véhicules.
     */
    public function vehicules(): void
    {
        Auth::requireLogin();
        $voitures = VoitureRepository::findByUser(Auth::id());
        $marques  = MarqueRepository::all();

        $this->view('user/vehicules', [
            'pageTitle' => 'Mes véhicules',
            'voitures'  => $voitures,
            'marques'   => $marques,
        ]);
    }

    public function addVehicule(): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $marqueId = isset($_POST['marque_id']) && $_POST['marque_id'] !== ''
            ? (int) $_POST['marque_id']
            : MarqueRepository::findOrCreate(trim((string) ($_POST['marque_nouvelle'] ?? '')));

        if ($marqueId === 0) {
            $this->flash('error', 'Marque invalide.');
            $this->redirect('/mon-espace/vehicules');
        }

        try {
            VoitureRepository::create([
                'utilisateur_id'   => Auth::id(),
                'marque_id'        => $marqueId,
                'modele'           => $this->input('modele'),
                'immatriculation'  => strtoupper($this->input('immatriculation') ?? ''),
                'energie'          => $this->input('energie'),
                'couleur'          => $this->input('couleur'),
                'date_premiere_immatriculation' => $this->input('date_premiere_immatriculation'),
                'nb_places'        => (int) ($_POST['nb_places'] ?? 4),
            ]);
            $this->flash('success', 'Véhicule ajouté.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect('/mon-espace/vehicules');
    }

    public function deleteVehicule(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();
        VoitureRepository::delete((int) $id, Auth::id());
        $this->flash('success', 'Véhicule supprimé.');
        $this->redirect('/mon-espace/vehicules');
    }

    /**
     * US 8 : Préférences chauffeur (stockées en MongoDB).
     */
    public function preferences(): void
    {
        Auth::requireLogin();
        $prefs = PreferenceRepository::findByUser(Auth::id());

        $this->view('user/preferences', [
            'pageTitle'   => 'Mes préférences',
            'preferences' => $prefs,
        ]);
    }

    public function updatePreferences(): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $prefs = [
            'fumeur'  => $_POST['fumeur']  ?? 'non',
            'animaux' => $_POST['animaux'] ?? 'non',
        ];

        // Préférences personnalisées (clés dynamiques)
        $customCles    = $_POST['pref_cle']    ?? [];
        $customValeurs = $_POST['pref_valeur'] ?? [];
        for ($i = 0; $i < count($customCles); $i++) {
            $cle = trim((string) ($customCles[$i] ?? ''));
            $val = trim((string) ($customValeurs[$i] ?? ''));
            if ($cle !== '' && $val !== '' && !in_array($cle, ['fumeur', 'animaux'], true)) {
                $prefs[$cle] = $val;
            }
        }

        try {
            PreferenceRepository::save(Auth::id(), $prefs);
            $this->flash('success', 'Préférences enregistrées.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/mon-espace/preferences');
    }

    /**
     * US 9 : Saisir un voyage (chauffeur).
     */
    public function newVoyage(): void
    {
        Auth::requireRole('chauffeur');
        $voitures = VoitureRepository::findByUser(Auth::id());

        $this->view('user/voyage-nouveau', [
            'pageTitle' => 'Proposer un voyage',
            'voitures'  => $voitures,
        ]);
    }

    public function createVoyage(): void
    {
        $this->verifyCsrf();
        Auth::requireRole('chauffeur');

        $voitureId = (int) ($_POST['voiture_id'] ?? 0);
        $voiture = VoitureRepository::findById($voitureId);
        if (!$voiture || (int) $voiture['utilisateur_id'] !== Auth::id()) {
            $this->flash('error', 'Véhicule invalide.');
            $this->redirect('/mon-espace/voyages/nouveau');
        }

        $prix = (float) ($_POST['prix_personne'] ?? 0);
        if ($prix < 3) {
            $this->flash('error', 'Le prix doit être au minimum de 3 crédits (dont 2 prélevés par la plateforme).');
            $this->redirect('/mon-espace/voyages/nouveau');
        }

        try {
            $covoiturageId = CovoiturageRepository::create([
                'chauffeur_id'  => Auth::id(),
                'voiture_id'    => $voitureId,
                'date_depart'   => $this->input('date_depart'),
                'heure_depart'  => $this->input('heure_depart'),
                'date_arrivee'  => $this->input('date_arrivee'),
                'heure_arrivee' => $this->input('heure_arrivee'),
                'lieu_depart'   => $this->input('lieu_depart'),
                'lieu_arrivee'  => $this->input('lieu_arrivee'),
                'nb_place'      => min((int) $voiture['nb_places'], (int) ($_POST['nb_place'] ?? 4)),
                'prix_personne' => $prix,
            ]);
            $this->flash('success', "Trajet créé (n°$covoiturageId). Il est maintenant visible des voyageurs.");
            $this->redirect('/mon-espace/historique');
        } catch (\Throwable $e) {
            $this->flash('error', 'Erreur : ' . $e->getMessage());
            $this->redirect('/mon-espace/voyages/nouveau');
        }
    }

    /**
     * US 10 : Historique des covoiturages.
     */
    public function historique(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        $user = UserRepository::findById($userId);

        $voyagesChauffeur = in_array('chauffeur', $user['roles'] ?? [], true)
            ? CovoiturageRepository::findByChauffeur($userId)
            : [];
        $voyagesPassager = CovoiturageRepository::findByPassager($userId);

        $this->view('user/historique', [
            'pageTitle'        => 'Mon historique',
            'voyagesChauffeur' => $voyagesChauffeur,
            'voyagesPassager'  => $voyagesPassager,
        ]);
    }

    /**
     * US 10 : Annuler un covoiturage (chauffeur ou passager).
     */
    public function annulerVoyage(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $covoiturage = CovoiturageRepository::findById((int) $id);
        if (!$covoiturage) {
            $this->flash('error', 'Covoiturage introuvable.');
            $this->redirect('/mon-espace/historique');
        }

        $userId = Auth::id();
        $isChauffeur = (int) $covoiturage['chauffeur_id'] === $userId;
        $isPassager  = ParticipationRepository::exists((int) $id, $userId);

        if (!$isChauffeur && !$isPassager) {
            $this->flash('error', 'Vous n\'êtes pas concerné par ce trajet.');
            $this->redirect('/mon-espace/historique');
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            if ($isChauffeur) {
                CovoiturageRepository::setStatut((int) $id, 'annule');
                // Rembourser tous les passagers
                foreach (ParticipationRepository::findByCovoiturage((int) $id) as $part) {
                    if ($part['statut_validation'] === 'en_attente') {
                        UserRepository::updateCredit((int) $part['passager_id'], (int) $covoiturage['prix_personne']);
                        ParticipationRepository::setStatut((int) $part['participation_id'], 'annule');
                    }
                }
                // En production : envoyer un mail aux passagers
                $this->flash('success', 'Trajet annulé. Les passagers ont été remboursés et notifiés.');
            } else {
                $stmt = $db->prepare(
                    'SELECT participation_id FROM participation
                     WHERE covoiturage_id = :cid AND passager_id = :uid AND statut_validation = "en_attente"'
                );
                $stmt->execute(['cid' => $id, 'uid' => $userId]);
                $partId = (int) $stmt->fetchColumn();
                if ($partId) {
                    ParticipationRepository::setStatut($partId, 'annule');
                    UserRepository::updateCredit($userId, (int) $covoiturage['prix_personne']);
                    Auth::setCredit((int) (Auth::user()['credit'] ?? 0) + (int) $covoiturage['prix_personne']);
                }
                $this->flash('success', 'Participation annulée. Vos crédits ont été restitués.');
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect('/mon-espace/historique');
    }

    /**
     * US 11 : Démarrer un covoiturage.
     */
    public function demarrerVoyage(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $covoiturage = CovoiturageRepository::findById((int) $id);
        if (!$covoiturage || (int) $covoiturage['chauffeur_id'] !== Auth::id()) {
            $this->flash('error', 'Trajet introuvable.');
            $this->redirect('/mon-espace/historique');
        }

        CovoiturageRepository::setStatut((int) $id, 'en_cours');
        $this->flash('success', 'Covoiturage démarré. Bonne route !');
        $this->redirect('/mon-espace/historique');
    }

    /**
     * US 11 : Arrivée à destination (notifie les passagers).
     */
    public function arriverVoyage(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $covoiturage = CovoiturageRepository::findById((int) $id);
        if (!$covoiturage || (int) $covoiturage['chauffeur_id'] !== Auth::id()) {
            $this->flash('error', 'Trajet introuvable.');
            $this->redirect('/mon-espace/historique');
        }

        CovoiturageRepository::setStatut((int) $id, 'termine');
        // En production : envoyer un mail aux passagers leur demandant de valider
        $this->flash('success', 'Covoiturage clôturé. Les passagers vont recevoir une demande de validation.');
        $this->redirect('/mon-espace/historique');
    }

    /**
     * US 11 : Validation post-trajet par un passager.
     */
    public function validerParticipation(string $id): void
    {
        $this->verifyCsrf();
        Auth::requireLogin();

        $part = ParticipationRepository::findById((int) $id);
        if (!$part || (int) $part['passager_id'] !== Auth::id()) {
            $this->flash('error', 'Participation introuvable.');
            $this->redirect('/mon-espace/historique');
        }

        $action      = $_POST['action'] ?? 'ok';
        $note        = (int) ($_POST['note'] ?? 0);
        $commentaire = $this->input('commentaire') ?? '';

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            if ($action === 'ok') {
                ParticipationRepository::setStatut((int) $id, 'valide_ok');

                $covoiturage = CovoiturageRepository::findById((int) $part['covoiturage_id']);
                $prix = (int) $covoiturage['prix_personne'];
                $commission = 2;

                UserRepository::updateCredit((int) $covoiturage['chauffeur_id'], $prix - $commission);
                $db->prepare(
                    'INSERT INTO credit_plateforme (covoiturage_id, montant) VALUES (:cid, :montant)'
                )->execute(['cid' => $covoiturage['covoiturage_id'], 'montant' => $commission]);

                if ($note >= 1 && $note <= 5) {
                    AvisRepository::create([
                        'auteur_id'      => Auth::id(),
                        'auteur_pseudo'  => Auth::user()['pseudo'] ?? '',
                        'chauffeur_id'   => (int) $covoiturage['chauffeur_id'],
                        'covoiturage_id' => (int) $covoiturage['covoiturage_id'],
                        'note'           => $note,
                        'commentaire'    => $commentaire,
                    ]);
                }

                $this->flash('success', 'Validation enregistrée. Merci !');
            } else {
                ParticipationRepository::setStatut((int) $id, 'valide_probleme', $commentaire);
                $this->flash('info', 'Un employé prendra contact avec le chauffeur pour résoudre la situation.');
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect('/mon-espace/historique');
    }
}
