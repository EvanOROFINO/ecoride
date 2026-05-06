<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Voiture
{
    public static function findByUser(int $userId): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT v.*, m.libelle AS marque_libelle
             FROM voiture v
             INNER JOIN marque m ON m.marque_id = v.marque_id
             WHERE v.utilisateur_id = :id
             ORDER BY v.voiture_id DESC'
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM voiture WHERE voiture_id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO voiture
                (utilisateur_id, marque_id, modele, immatriculation, energie, couleur, date_premiere_immatriculation, nb_places)
             VALUES
                (:utilisateur_id, :marque_id, :modele, :immatriculation, :energie, :couleur, :date_premiere_immatriculation, :nb_places)'
        );
        $stmt->execute($data);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function delete(int $id, int $userId): bool
    {
        $stmt = Database::getInstance()->prepare(
            'DELETE FROM voiture WHERE voiture_id = :id AND utilisateur_id = :uid'
        );
        return $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
