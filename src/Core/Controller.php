<?php
/**
 * Classe de base pour tous les controllers.
 * Fournit des helpers de rendu de vue, de redirection et de réponse JSON.
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Rend une vue avec un layout (par défaut : main).
     *
     * @param string $view  Nom de la vue (ex : "home/index")
     * @param array  $data  Variables passées à la vue
     * @param string $layout Layout à utiliser (par défaut : main)
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue introuvable : $view");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Redirige vers une URL et arrête le script.
     */
    protected function redirect(string $url, int $code = 302): never
    {
        header("Location: $url", true, $code);
        exit;
    }

    /**
     * Réponse JSON.
     */
    protected function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Récupère une valeur POST en l'assainissant.
     */
    protected function input(string $key, ?string $default = null): ?string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? Security::sanitize($value) : $default;
    }

    /**
     * Vérifie le jeton CSRF d'une requête POST. Renvoie 419 si invalide.
     */
    protected function verifyCsrf(): void
    {
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            $_SESSION['flash_error'] = 'Session expirée, veuillez réessayer.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    /**
     * Stocke un message flash pour la prochaine requête.
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION["flash_$type"] = $message;
    }
}
