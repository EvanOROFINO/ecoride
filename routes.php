<?php
/**
 * Définition des routes de l'application.
 * Format : $router->method('/chemin', [Controller::class, 'methode']);
 */

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CovoiturageController;
use App\Controllers\UserController;
use App\Controllers\EmployeController;
use App\Controllers\AdminController;
use App\Controllers\ContactController;

// ---------- Pages publiques (visiteur) ----------
$router->get('/',                  [HomeController::class, 'index']);
$router->get('/mentions-legales',  [HomeController::class, 'mentionsLegales']);
$router->get('/contact',           [ContactController::class, 'show']);
$router->post('/contact',          [ContactController::class, 'send']);

// ---------- Authentification ----------
$router->get('/login',             [AuthController::class, 'showLogin']);
$router->post('/login',            [AuthController::class, 'login']);
$router->get('/register',          [AuthController::class, 'showRegister']);
$router->post('/register',         [AuthController::class, 'register']);
$router->post('/logout',           [AuthController::class, 'logout']);

// ---------- API JSON (utilisée par les fichiers .js avec fetch()) ----------
$router->get('/api/auth/check',    [AuthController::class, 'apiCheckAvailability']);
$router->get('/api/covoiturages',  [CovoiturageController::class, 'apiSearch']);

// ---------- Covoiturages (visiteur + utilisateur) ----------
$router->get('/covoiturages',      [CovoiturageController::class, 'index']);
$router->get('/covoiturages/{id}', [CovoiturageController::class, 'show']);
$router->post('/covoiturages/{id}/participer', [CovoiturageController::class, 'participer']);

// ---------- Espace utilisateur ----------
$router->get('/mon-espace',                    [UserController::class, 'dashboard']);
$router->post('/mon-espace/role',              [UserController::class, 'updateRole']);
$router->get('/mon-espace/vehicules',          [UserController::class, 'vehicules']);
$router->post('/mon-espace/vehicules',         [UserController::class, 'addVehicule']);
$router->post('/mon-espace/vehicules/{id}/supprimer', [UserController::class, 'deleteVehicule']);
$router->get('/mon-espace/preferences',        [UserController::class, 'preferences']);
$router->post('/mon-espace/preferences',       [UserController::class, 'updatePreferences']);
$router->get('/mon-espace/voyages/nouveau',    [UserController::class, 'newVoyage']);
$router->post('/mon-espace/voyages/nouveau',   [UserController::class, 'createVoyage']);
$router->get('/mon-espace/historique',         [UserController::class, 'historique']);
$router->post('/mon-espace/voyages/{id}/annuler',  [UserController::class, 'annulerVoyage']);
$router->post('/mon-espace/voyages/{id}/demarrer', [UserController::class, 'demarrerVoyage']);
$router->post('/mon-espace/voyages/{id}/arriver',  [UserController::class, 'arriverVoyage']);
$router->post('/mon-espace/participations/{id}/valider', [UserController::class, 'validerParticipation']);

// ---------- Espace employé ----------
$router->get('/employe',                       [EmployeController::class, 'dashboard']);
$router->get('/employe/avis',                  [EmployeController::class, 'avisEnAttente']);
$router->post('/employe/avis/{id}/valider',    [EmployeController::class, 'validerAvis']);
$router->post('/employe/avis/{id}/refuser',    [EmployeController::class, 'refuserAvis']);
$router->get('/employe/incidents',             [EmployeController::class, 'incidents']);

// ---------- Espace administrateur ----------
$router->get('/admin',                         [AdminController::class, 'dashboard']);
$router->get('/admin/employes',                [AdminController::class, 'employes']);
$router->post('/admin/employes',               [AdminController::class, 'createEmploye']);
$router->post('/admin/utilisateurs/{id}/suspendre', [AdminController::class, 'suspendre']);
$router->post('/admin/utilisateurs/{id}/reactiver', [AdminController::class, 'reactiver']);
$router->get('/admin/api/stats',               [AdminController::class, 'apiStats']);
