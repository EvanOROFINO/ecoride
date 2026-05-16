<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Security;
use App\Repositories\UserRepository;

final class AuthController extends Controller
{
    /**
     * Endpoint JSON utilisé par public/js/register-validation.js (via fetch)
     * pour vérifier en temps réel si un pseudo ou email est disponible.
     *
     * Usage : GET /api/auth/check?pseudo=xxx  ou  /api/auth/check?email=xxx
     */
    public function apiCheckAvailability(): void
    {
        $pseudo = trim((string) ($_GET['pseudo'] ?? ''));
        $email  = trim((string) ($_GET['email']  ?? ''));

        if ($pseudo !== '') {
            $exists = UserRepository::findByPseudo($pseudo) !== null;
            $this->json(['field' => 'pseudo', 'value' => $pseudo, 'available' => !$exists]);
        }

        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json(['field' => 'email', 'available' => false, 'error' => 'Format invalide'], 400);
            }
            $exists = UserRepository::findByEmail($email) !== null;
            $this->json(['field' => 'email', 'value' => $email, 'available' => !$exists]);
        }

        $this->json(['error' => 'Paramètre pseudo ou email requis.'], 400);
    }

    public function showLogin(): void
    {
        if (Auth::check()) $this->redirect('/mon-espace');
        $this->view('auth/login', ['pageTitle' => 'Connexion']);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = $this->input('email')    ?? '';
        $password = $_POST['password']       ?? '';
        $next     = $this->input('next')     ?? '/mon-espace';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->flash('error', 'Email ou mot de passe invalide.');
            $this->redirect('/login');
        }

        $user = UserRepository::findByEmail($email);
        if (!$user || !Security::verifyPassword($password, $user['password_hash'])) {
            $this->flash('error', 'Identifiants incorrects.');
            $this->redirect('/login');
        }

        if ($user['statut'] === 'suspendu') {
            $this->flash('error', 'Votre compte a été suspendu. Contactez le support.');
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->flash('success', 'Bienvenue, ' . $user['pseudo'] . ' !');
        $this->redirect($next);
    }

    public function showRegister(): void
    {
        if (Auth::check()) $this->redirect('/mon-espace');
        $this->view('auth/register', ['pageTitle' => 'Créer un compte']);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $pseudo   = $this->input('pseudo')   ?? '';
        $email    = $this->input('email')    ?? '';
        $password = $_POST['password']       ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];
        if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
            $errors[] = 'Le pseudo doit contenir entre 3 et 50 caractères.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if ($pwError = Security::validatePasswordStrength($password)) {
            $errors[] = $pwError;
        }
        if (UserRepository::findByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cet email.';
        }
        if (UserRepository::findByPseudo($pseudo)) {
            $errors[] = 'Ce pseudo est déjà pris.';
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/register');
        }

        try {
            $userId = UserRepository::create($pseudo, $email, $password);
            $user   = UserRepository::findById($userId);
            Auth::login($user);
            $this->flash('success', 'Compte créé ! Vous bénéficiez de 20 crédits offerts.');
            $this->redirect('/mon-espace');
        } catch (\Throwable $e) {
            $this->flash('error', 'Erreur lors de la création du compte. Réessayez.');
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Auth::logout();
        $this->flash('success', 'Vous êtes déconnecté.');
        $this->redirect('/');
    }
}
