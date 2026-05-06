<?php
/**
 * Fonctions utilitaires globales utilisables dans toute l'application.
 */

declare(strict_types=1);

use App\Core\Security;

if (!function_exists('e')) {
    /**
     * Échappement HTML court (XSS).
     */
    function e(?string $value): string
    {
        return Security::escape($value);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Champ caché CSRF à inclure dans les formulaires POST.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(Security::csrfToken()) . '">';
    }
}

if (!function_exists('asset')) {
    /**
     * URL d'un asset public (CSS, JS, image).
     */
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * URL absolue à partir d'un chemin relatif.
     */
    function url(string $path = '/'): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        return rtrim($config['app']['url'], '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('flash')) {
    /**
     * Récupère et efface un message flash.
     */
    function flash(string $type): ?string
    {
        $key = "flash_$type";
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }
}

if (!function_exists('format_date_fr')) {
    /**
     * Formate une date au format français (ex : 12 mai 2026).
     */
    function format_date_fr(string $date): string
    {
        $months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
        $ts = strtotime($date);
        if ($ts === false) {
            return $date;
        }
        return (int) date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
}

if (!function_exists('format_duree')) {
    /**
     * Formate une durée en minutes vers "Xh Ymin".
     */
    function format_duree(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h === 0) return "{$m}min";
        if ($m === 0) return "{$h}h";
        return "{$h}h{$m}min";
    }
}

if (!function_exists('duree_minutes')) {
    /**
     * Calcule la durée en minutes entre deux datetime.
     */
    function duree_minutes(string $dateStart, string $timeStart, string $dateEnd, string $timeEnd): int
    {
        $start = strtotime("$dateStart $timeStart");
        $end   = strtotime("$dateEnd $timeEnd");
        if ($start === false || $end === false) return 0;
        return max(0, (int) (($end - $start) / 60));
    }
}
