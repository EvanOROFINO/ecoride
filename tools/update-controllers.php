<?php
/**
 * Script de migration : remplace les usages de App\Models\X par App\Repositories\XRepository
 * dans les Controllers, Views et autres fichiers PHP.
 */

declare(strict_types=1);

$targets = [
    __DIR__ . '/../src/Controllers',
    __DIR__ . '/../src/Core',
    __DIR__ . '/../src/Helpers',
    __DIR__ . '/../views',
    __DIR__ . '/../routes.php',
    __DIR__ . '/../public/index.php',
];

// Récupère tous les noms de Models existants
$modelFiles = glob(__DIR__ . '/../src/Models/*.php');
$modelNames = array_map(fn($f) => pathinfo($f, PATHINFO_FILENAME), $modelFiles);

$filesChanged = 0;
$replacements = 0;

foreach ($targets as $target) {
    if (is_file($target)) {
        $files = [$target];
    } else {
        $files = glob($target . '/*.php');
    }
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $original = $content;

        foreach ($modelNames as $name) {
            $newName = $name . 'Repository';
            // Remplacer dans use statements
            $content = str_replace("use App\\Models\\{$name};", "use App\\Repositories\\{$newName};", $content);
            // Remplacer dans utilisations (X::method ou new X)
            $content = preg_replace('/\b' . preg_quote($name, '/') . '::/', $newName . '::', $content);
        }

        if ($content !== $original) {
            file_put_contents($file, $content);
            $filesChanged++;
            $replacements++;
            echo "OK " . basename($file) . "\n";
        }
    }
}

echo "\n$filesChanged fichiers modifiés.\n";
