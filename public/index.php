<?php
/**
 * Front controller — point d'entrée unique de l'application EcoRide.
 * Toutes les requêtes HTTP passent par ici (via .htaccess).
 */

declare(strict_types=1);

// Configuration des erreurs selon l'environnement
$config = require __DIR__ . '/../config/config.php';
if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Démarrage de session sécurisée
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');
session_start();

// Autoload Composer + helpers
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Helpers/functions.php';

// Chargement des routes
$router = new App\Core\Router();
require __DIR__ . '/../routes.php';

// Dispatch
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';

try {
    $router->dispatch($method, $uri);
} catch (Throwable $e) {
    if ($config['app']['debug']) {
        echo '<pre style="padding:20px;background:#fee;color:#900;font-family:monospace">';
        echo 'Erreur : ' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo $e->getTraceAsString();
        echo '</pre>';
    } else {
        http_response_code(500);
        require __DIR__ . '/../views/errors/500.php';
    }
}
