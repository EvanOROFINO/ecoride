<?php
/**
 * Modèle Avis : stocké dans MongoDB pour profiter de la flexibilité
 * du schéma et de la modération asynchrone.
 */
declare(strict_types=1);

namespace App\Models;

use App\Core\Mongo;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

final class Avis
{
    public static function create(array $data): string
    {
        $data['date_creation'] = new UTCDateTime();
        $data['statut'] = 'en_attente';
        $result = Mongo::getInstance()->avis->insertOne($data);
        return (string) $result->getInsertedId();
    }

    /**
     * Avis validés d'un chauffeur (pour la page détail covoiturage).
     * Fallback : tableau vide si MongoDB indisponible (dev sans Mongo).
     */
    public static function findValidesForChauffeur(int $chauffeurId): array
    {
        try {
            $cursor = Mongo::getInstance()->avis->find(
                ['chauffeur_id' => $chauffeurId, 'statut' => 'valide'],
                ['sort' => ['date_creation' => -1]]
            );
            return iterator_to_array($cursor);
        } catch (\Throwable $e) {
            error_log('MongoDB indisponible : ' . $e->getMessage());
            return [];
        }
    }

    public static function findEnAttente(): array
    {
        try {
            $cursor = Mongo::getInstance()->avis->find(
                ['statut' => 'en_attente'],
                ['sort' => ['date_creation' => 1]]
            );
            return iterator_to_array($cursor);
        } catch (\Throwable $e) {
            error_log('MongoDB indisponible : ' . $e->getMessage());
            return [];
        }
    }

    public static function valider(string $id, int $employeId): void
    {
        Mongo::getInstance()->avis->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => [
                'statut'           => 'valide',
                'date_validation'  => new UTCDateTime(),
                'validateur_id'    => $employeId,
            ]]
        );
    }

    public static function refuser(string $id, int $employeId): void
    {
        Mongo::getInstance()->avis->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => [
                'statut'           => 'refuse',
                'date_validation'  => new UTCDateTime(),
                'validateur_id'    => $employeId,
            ]]
        );
    }
}
