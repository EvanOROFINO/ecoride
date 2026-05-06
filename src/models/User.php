<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Security;
use PDO;

final class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM utilisateur WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if (!$user) return null;

        $user['roles'] = self::getRoles((int) $user['utilisateur_id']);
        return $user;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM utilisateur WHERE utilisateur_id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) return null;

        $user['roles'] = self::getRoles($id);
        return $user;
    }

    public static function findByPseudo(string $pseudo): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT * FROM utilisateur WHERE pseudo = :pseudo LIMIT 1'
        );
        $stmt->execute(['pseudo' => $pseudo]);
        return $stmt->fetch() ?: null;
    }

    public static function getRoles(int $userId): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT r.libelle FROM role r
             INNER JOIN utilisateur_role ur ON ur.role_id = r.role_id
             WHERE ur.utilisateur_id = :id'
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Crée un compte utilisateur (US 7).
     * Le mot de passe doit déjà avoir été validé en amont (Security::validatePasswordStrength).
     */
    public static function create(string $pseudo, string $email, string $password): int
    {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                'INSERT INTO utilisateur (pseudo, email, password_hash, credit, statut)
                 VALUES (:pseudo, :email, :hash, 20, "actif")'
            );
            $stmt->execute([
                'pseudo' => $pseudo,
                'email'  => $email,
                'hash'   => Security::hashPassword($password),
            ]);
            $userId = (int) $db->lastInsertId();

            // Rôle "utilisateur" par défaut
            $stmt = $db->prepare(
                'INSERT INTO utilisateur_role (utilisateur_id, role_id)
                 SELECT :uid, role_id FROM role WHERE libelle = "utilisateur"'
            );
            $stmt->execute(['uid' => $userId]);

            $db->commit();
            return $userId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateCredit(int $userId, int $delta): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE utilisateur SET credit = credit + :delta WHERE utilisateur_id = :id'
        );
        $stmt->execute(['delta' => $delta, 'id' => $userId]);
    }

    public static function setRoles(int $userId, array $roleLibelles): void
    {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM utilisateur_role WHERE utilisateur_id = :id')
               ->execute(['id' => $userId]);

            $stmt = $db->prepare(
                'INSERT INTO utilisateur_role (utilisateur_id, role_id)
                 SELECT :uid, role_id FROM role WHERE libelle = :lib'
            );
            foreach (array_unique($roleLibelles) as $libelle) {
                $stmt->execute(['uid' => $userId, 'lib' => $libelle]);
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function setStatut(int $userId, string $statut): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE utilisateur SET statut = :statut WHERE utilisateur_id = :id'
        );
        $stmt->execute(['statut' => $statut, 'id' => $userId]);
    }

    /**
     * Note moyenne d'un chauffeur (calculée depuis les avis validés).
     */
    public static function getAverageRating(int $userId): ?float
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT AVG(note) AS moyenne FROM avis
             WHERE chauffeur_id = :id AND statut = "valide"'
        );
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch();
        return $result['moyenne'] !== null ? round((float) $result['moyenne'], 1) : null;
    }
}
