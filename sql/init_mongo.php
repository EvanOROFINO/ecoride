<?php
/**
 * Script d'initialisation de la base MongoDB d'EcoRide.
 *
 * Crée les collections et insère un jeu de données de test.
 * À exécuter une fois après installation : php sql/init_mongo.php
 *
 * Collections créées :
 *  - avis           : avis et notes laissés par les passagers (modération asynchrone)
 *  - preferences    : préférences extensibles des chauffeurs (clé/valeur libre)
 *  - configuration  : paramètres globaux de la plateforme (lecture fréquente, écriture rare)
 *  - logs           : journal d'événements applicatifs (audit / debug)
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$client = new MongoDB\Client($config['mongo']['uri']);
$db     = $client->selectDatabase($config['mongo']['db']);

echo "Connexion à MongoDB : {$config['mongo']['uri']}\n";
echo "Base : {$config['mongo']['db']}\n\n";

// ---------- Nettoyage (utile en dev) ----------
foreach (['avis','preferences','configuration','logs'] as $col) {
    $db->{$col}->drop();
}
echo "Collections nettoyées.\n";

// ---------- Index ----------
$db->avis->createIndex(['chauffeur_id' => 1, 'statut' => 1]);
$db->avis->createIndex(['date_creation' => -1]);
$db->preferences->createIndex(['utilisateur_id' => 1]);
$db->configuration->createIndex(['cle' => 1], ['unique' => true]);
$db->logs->createIndex(['date' => -1]);
$db->logs->createIndex(['niveau' => 1]);
echo "Index créés.\n";

// ---------- Avis (jeu de données) ----------
$db->avis->insertMany([
    [
        'auteur_id'      => 5,
        'auteur_pseudo'  => 'emma_l',
        'chauffeur_id'   => 3,
        'covoiturage_id' => 7,
        'note'           => 5,
        'commentaire'    => 'Trajet très agréable, conductrice ponctuelle et sympa !',
        'statut'         => 'valide',
        'date_creation'  => new MongoDB\BSON\UTCDateTime((time() - 4 * 86400) * 1000),
        'date_validation'=> new MongoDB\BSON\UTCDateTime((time() - 4 * 86400) * 1000),
    ],
    [
        'auteur_id'      => 8,
        'auteur_pseudo'  => 'paul_g',
        'chauffeur_id'   => 3,
        'covoiturage_id' => 7,
        'note'           => 4,
        'commentaire'    => 'Très bien dans l\'ensemble, je recommande.',
        'statut'         => 'valide',
        'date_creation'  => new MongoDB\BSON\UTCDateTime((time() - 4 * 86400) * 1000),
        'date_validation'=> new MongoDB\BSON\UTCDateTime((time() - 3 * 86400) * 1000),
    ],
    [
        'auteur_id'      => 6,
        'auteur_pseudo'  => 'tom_d',
        'chauffeur_id'   => 4,
        'covoiturage_id' => 8,
        'note'           => 4,
        'commentaire'    => 'Bon trajet, voiture confortable.',
        'statut'         => 'en_attente',
        'date_creation'  => new MongoDB\BSON\UTCDateTime((time() - 86400) * 1000),
    ],
]);
echo "Avis insérés.\n";

// ---------- Préférences chauffeurs ----------
$db->preferences->insertMany([
    [
        'utilisateur_id' => 3,
        'preferences'    => [
            'fumeur'      => 'non',
            'animaux'     => 'oui',
            'musique'     => 'oui',
            'discussion'  => 'modérée',
        ],
        'maj' => new MongoDB\BSON\UTCDateTime(),
    ],
    [
        'utilisateur_id' => 4,
        'preferences'    => [
            'fumeur'      => 'non',
            'animaux'     => 'non',
            'discussion'  => 'modérée',
        ],
        'maj' => new MongoDB\BSON\UTCDateTime(),
    ],
    [
        'utilisateur_id' => 7,
        'preferences'    => [
            'fumeur'        => 'non',
            'animaux'       => 'oui',
            'climatisation' => 'oui',
            'pause_pipi'    => 'toutes les 2h',
        ],
        'maj' => new MongoDB\BSON\UTCDateTime(),
    ],
]);
echo "Préférences insérées.\n";

// ---------- Configuration globale ----------
$db->configuration->insertMany([
    ['cle' => 'commission_plateforme',     'valeur' => 2,    'description' => 'Crédits prélevés par trajet'],
    ['cle' => 'credits_inscription',       'valeur' => 20,   'description' => 'Crédits offerts à l\'inscription'],
    ['cle' => 'note_min_filtre',           'valeur' => 1,    'description' => 'Note minimale possible dans les filtres'],
    ['cle' => 'maintenance',               'valeur' => false, 'description' => 'Active le mode maintenance'],
]);
echo "Configuration insérée.\n";

// ---------- Logs (exemple) ----------
$db->logs->insertOne([
    'date'    => new MongoDB\BSON\UTCDateTime(),
    'niveau'  => 'info',
    'message' => 'Initialisation de la base MongoDB EcoRide',
    'context' => ['script' => 'init_mongo.php'],
]);
echo "Logs initialisés.\n";

echo "\nMongoDB prêt à l'emploi.\n";
echo "Statistiques :\n";
echo "  - avis           : " . $db->avis->countDocuments() . "\n";
echo "  - preferences    : " . $db->preferences->countDocuments() . "\n";
echo "  - configuration  : " . $db->configuration->countDocuments() . "\n";
echo "  - logs           : " . $db->logs->countDocuments() . "\n";
