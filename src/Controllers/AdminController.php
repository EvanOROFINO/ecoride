<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Repositories\StatsRepository;
use App\Repositories\UserRepository;
use PDO;

final class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        Auth::requireRole('administrateur');
    }

    public function dashboard(): void
    {
        $this->ensureAdmin();

        $this->view('admin/dashboard', [
            'pageTitle'         => 'Administration',
            'totalCredits'      => StatsRepository::totalCredits(),
            'totalUtilisateurs' => StatsRepository::totalUtilisateurs(),
            'totalCovoiturages' => StatsRepository::totalCovoiturages(),
        ]);
    }

    public function employes(): void
    {
        $this->ensureAdmin();

        $sql = 'SELECT u.utilisateur_id, u.pseudo, u.email, u.statut, u.date_creation,
                       GROUP_CONCAT(r.libelle) AS roles
                FROM utilisateur u
                LEFT JOIN utilisateur_role ur ON ur.utilisateur_id = u.utilisateur_id
                LEFT JOIN role r              ON r.role_id = ur.role_id
                GROUP BY u.utilisateur_id
                ORDER BY u.date_creation DESC';
        $users = Database::getInstance()->query($sql)->fetchAll();

        $this->view('admin/employes', [
            'pageTitle' => 'Gestion des comptes',
            'users'     => $users,
        ]);
    }

    public function createEmploye(): void
    {
        $this->verifyCsrf();
        $this->ensureAdmin();

        $pseudo   = $this->input('pseudo')   ?? '';
        $email    = $this->input('email')    ?? '';
        $password = $_POST['password']       ?? '';

        if ($pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || Security::validatePasswordStrength($password)) {
            $this->flash('error', 'Données invalides (pseudo, email, mot de passe sécurisé requis).');
            $this->redirect('/admin/employes');
        }

        if (UserRepository::findByEmail($email)) {
            $this->flash('error', 'Cet email est déjà utilisé.');
            $this->redirect('/admin/employes');
        }

        try {
            $userId = UserRepository::create($pseudo, $email, $password);
            UserRepository::setRoles($userId, ['utilisateur', 'employe']);
            $this->flash('success', "Compte employé créé : $pseudo");
        } catch (\Throwable $e) {
            $this->flash('error', 'Erreur : ' . $e->getMessage());
        }

        $this->redirect('/admin/employes');
    }

    public function suspendre(string $id): void
    {
        $this->verifyCsrf();
        $this->ensureAdmin();

        if ((int) $id === Auth::id()) {
            $this->flash('error', 'Vous ne pouvez pas vous suspendre vous-même.');
            $this->redirect('/admin/employes');
        }

        UserRepository::setStatut((int) $id, 'suspendu');
        $this->flash('success', 'Compte suspendu.');
        $this->redirect('/admin/employes');
    }

    public function reactiver(string $id): void
    {
        $this->verifyCsrf();
        $this->ensureAdmin();

        UserRepository::setStatut((int) $id, 'actif');
        $this->flash('success', 'Compte réactivé.');
        $this->redirect('/admin/employes');
    }

    /**
     * API JSON pour les graphiques du dashboard (US 13).
     */
    public function apiStats(): void
    {
        $this->ensureAdmin();
        $jours = isset($_GET['jours']) ? max(7, min(365, (int) $_GET['jours'])) : 30;

        $this->json([
            'covoituragesParJour' => StatsRepository::covoituragesParJour($jours),
            'creditsParJour'      => StatsRepository::creditsParJour($jours),
            'totalCredits'        => StatsRepository::totalCredits(),
            'jours'               => $jours,
        ]);
    }
}
