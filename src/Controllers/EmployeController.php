<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Repositories\AvisRepository;

final class EmployeController extends Controller
{
    private function ensureEmploye(): void
    {
        Auth::requireRole(['employe', 'administrateur']);
    }

    public function dashboard(): void
    {
        $this->ensureEmploye();

        $avisAttente = AvisRepository::findEnAttente();
        $incidents = $this->getIncidents();

        $this->view('employe/dashboard', [
            'pageTitle'   => 'Espace employé',
            'nbAvis'      => count($avisAttente),
            'nbIncidents' => count($incidents),
        ]);
    }

    public function avisEnAttente(): void
    {
        $this->ensureEmploye();
        $avis = AvisRepository::findEnAttente();
        $this->view('employe/avis', [
            'pageTitle' => 'Avis à modérer',
            'avis'      => $avis,
        ]);
    }

    public function validerAvis(string $id): void
    {
        $this->verifyCsrf();
        $this->ensureEmploye();
        AvisRepository::valider($id, Auth::id());
        $this->flash('success', 'Avis validé.');
        $this->redirect('/employe/avis');
    }

    public function refuserAvis(string $id): void
    {
        $this->verifyCsrf();
        $this->ensureEmploye();
        AvisRepository::refuser($id, Auth::id());
        $this->flash('success', 'Avis refusé.');
        $this->redirect('/employe/avis');
    }

    public function incidents(): void
    {
        $this->ensureEmploye();
        $incidents = $this->getIncidents();
        $this->view('employe/incidents', [
            'pageTitle'  => 'Incidents',
            'incidents'  => $incidents,
        ]);
    }

    /**
     * Récupère les trajets ayant un problème (statut_validation = "valide_probleme")
     * avec les infos chauffeur, passager, trajet (US 12).
     */
    private function getIncidents(): array
    {
        $sql = 'SELECT
                    p.participation_id,
                    p.commentaire_probleme,
                    p.date_inscription,
                    c.covoiturage_id,
                    c.lieu_depart,
                    c.lieu_arrivee,
                    c.date_depart,
                    c.heure_depart,
                    c.date_arrivee,
                    c.heure_arrivee,
                    chauffeur.pseudo AS chauffeur_pseudo,
                    chauffeur.email  AS chauffeur_email,
                    passager.pseudo  AS passager_pseudo,
                    passager.email   AS passager_email
                FROM participation p
                INNER JOIN covoiturage c       ON c.covoiturage_id = p.covoiturage_id
                INNER JOIN utilisateur chauffeur ON chauffeur.utilisateur_id = c.chauffeur_id
                INNER JOIN utilisateur passager  ON passager.utilisateur_id = p.passager_id
                WHERE p.statut_validation = "valide_probleme"
                ORDER BY p.date_inscription DESC';
        return Database::getInstance()->query($sql)->fetchAll();
    }
}
