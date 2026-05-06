<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Marque
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query('SELECT * FROM marque ORDER BY libelle')
            ->fetchAll();
    }

    public static function findOrCreate(string $libelle): int
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT marque_id FROM marque WHERE libelle = :lib'
        );
        $stmt->execute(['lib' => $libelle]);
        $row = $stmt->fetch();
        if ($row) return (int) $row['marque_id'];

        $stmt = Database::getInstance()->prepare(
            'INSERT INTO marque (libelle) VALUES (:lib)'
        );
        $stmt->execute(['lib' => $libelle]);
        return (int) Database::getInstance()->lastInsertId();
    }
}
