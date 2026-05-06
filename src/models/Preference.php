<?php
/**
 * Modèle Preference : stockage des préférences chauffeur en MongoDB.
 * Format clé/valeur libre pour permettre l'extensibilité (US 8).
 */
declare(strict_types=1);

namespace App\Models;

use App\Core\Mongo;
use MongoDB\BSON\UTCDateTime;

final class Preference
{
    public static function findByUser(int $userId): array
    {
        try {
            $doc = Mongo::getInstance()->preferences->findOne(['utilisateur_id' => $userId]);
            if (!$doc) return [];

            $prefs = $doc['preferences'] ?? [];
            return is_object($prefs) ? (array) $prefs : (array) $prefs;
        } catch (\Throwable $e) {
            error_log('MongoDB indisponible : ' . $e->getMessage());
            return [];
        }
    }

    public static function save(int $userId, array $preferences): void
    {
        try {
            Mongo::getInstance()->preferences->updateOne(
                ['utilisateur_id' => $userId],
                ['$set' => [
                    'utilisateur_id' => $userId,
                    'preferences'    => $preferences,
                    'maj'            => new UTCDateTime(),
                ]],
                ['upsert' => true]
            );
        } catch (\Throwable $e) {
            error_log('MongoDB indisponible : ' . $e->getMessage());
            throw new \RuntimeException('Impossible d\'enregistrer les préférences. Réessayez plus tard.');
        }
    }
}
