<?php
/**
 * Singleton MongoDB.
 * Renvoie une instance \MongoDB\Database réutilisable.
 */

declare(strict_types=1);

namespace App\Core;

use MongoDB\Client;
use MongoDB\Database as MongoDatabase;
use RuntimeException;
use Throwable;

final class Mongo
{
    private static ?MongoDatabase $instance = null;

    private function __construct() {}

    public static function getInstance(): MongoDatabase
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $mongo = $config['mongo'];

            try {
                $client = new Client($mongo['uri']);
                self::$instance = $client->selectDatabase($mongo['db']);
            } catch (Throwable $e) {
                throw new RuntimeException(
                    'Connexion à MongoDB impossible : ' . $e->getMessage(),
                    (int) $e->getCode(),
                );
            }
        }

        return self::$instance;
    }
}
