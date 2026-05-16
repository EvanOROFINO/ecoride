<?php
/**
 * Script de refactoring : copie les fichiers de src/Models/ vers src/Repositories/
 * en adaptant les namespaces et noms de classes.
 *
 * Usage : php tools/make-repositories.php
 */
declare(strict_types=1);

$src = __DIR__ . '/../src/Models';
$dst = __DIR__ . '/../src/Repositories';

if (!is_dir($dst)) {
    mkdir($dst, 0755, true);
}

$files = glob($src . '/*.php');
$created = 0;

foreach ($files as $file) {
    $name = pathinfo($file, PATHINFO_FILENAME);   // ex: "Menu"
    $newName = $name . 'Repository';              // ex: "MenuRepository"
    $content = file_get_contents($file);

    // 1) Adapter le namespace
    $content = str_replace(
        'namespace App\\Models;',
        'namespace App\\Repositories;',
        $content
    );

    // 2) Adapter le nom de classe (une seule fois)
    // Match "final class Name" ou "class Name" suivi d'un espace ou accolade
    $pattern = '/\b(final\s+)?class\s+' . preg_quote($name, '/') . '\b/';
    $replaceCount = 0;
    $content = preg_replace($pattern, '$1class ' . $newName, $content, 1, $replaceCount);

    // 3) Adapter le commentaire de tête "Modèle ..." en "Repository ..."
    $content = preg_replace(
        '/^(\s*\*\s*)Mod[èe]le\s+/m',
        '$1Repository — Accès aux données ',
        $content
    );

    file_put_contents($dst . '/' . $newName . '.php', $content);
    echo "OK $name -> $newName (remplacements classe: $replaceCount)\n";
    $created++;
}

echo "\n$created Repositories générés dans $dst\n";
