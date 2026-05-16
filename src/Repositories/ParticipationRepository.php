<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ParticipationRepository
{
    public static function exists(int $covoiturageId, int $userId): bool
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT 1 FROM participation
             WHERE covoiturage_id = :cid AND passager_id = :uid AND statut_validation != "annule"'
        );
        $stmt->execute(['cid' => $covoiturageId, 'uid' => $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(int $covoiturageId, int $userId): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO participation (covoiturage_id, passager_id) VALUES (:cid, :uid)'
        );
        $stmt->execute(['cid' => $covoiturageId, 'uid' => $userId]);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM participation WHERE participation_id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByCovoiturage(int $covoiturageId): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT p.*, u.pseudo, u.email
             FROM participation p
             INNER JOIN utilisateur u ON u.utilisateur_id = p.passager_id
             WHERE p.covoiturage_id = :cid'
        );
        $stmt->execute(['cid' => $covoiturageId]);
        return $stmt->fetchAll();
    }

    public static function setStatut(int $id, string $statut, ?string $commentaire = null): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE participation
             SET statut_validation = :statut, commentaire_probleme = :commentaire
             WHERE participation_id = :id'
        );
        $stmt->execute(['statut' => $statut, 'commentaire' => $commentaire, 'id' => $id]);
    }
}
