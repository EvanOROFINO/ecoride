<?php
/**
 * Helpers de sécurité : CSRF, validation mot de passe, échappement.
 */

declare(strict_types=1);

namespace App\Core;

final class Security
{
    /**
     * Génère ou récupère un jeton CSRF pour le formulaire courant.
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie qu'un jeton CSRF reçu correspond au jeton de session.
     */
    public static function verifyCsrf(?string $token): bool
    {
        if ($token === null || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Échappe une chaîne pour un affichage HTML (XSS).
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Valide la robustesse d'un mot de passe (min 8 char, 1 maj, 1 min, 1 chiffre, 1 spécial).
     * Retourne null si OK, sinon un message d'erreur.
     */
    public static function validatePasswordStrength(string $password): ?string
    {
        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/\d/', $password)) {
            return 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Le mot de passe doit contenir au moins un caractère spécial.';
        }

        return null;
    }

    /**
     * Hache un mot de passe avec bcrypt (coût par défaut PHP).
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Vérifie un mot de passe contre son hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize une entrée utilisateur (trim + suppression caractères invisibles).
     */
    public static function sanitize(?string $input): string
    {
        if ($input === null) {
            return '';
        }

        return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input));
    }
}
