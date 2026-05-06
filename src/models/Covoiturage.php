<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Covoiturage
{
    /**
     * Recherche des covoiturages par ville de départ, ville d'arrivée et date.
     * Retourne uniquement ceux avec au moins 1 place restante.
     *
     * @return array<int, array> Liste des covoiturages avec infos chauffeur et voiture
     */
    public static function search(string $depart, string $arrivee, string $date): array
    {
        $sql = 'SELECT
                    c.*,
                    u.pseudo AS chauffeur_pseudo,
                    u.photo  AS chauffeur_photo,
                    u.utilisateur_id AS chauffeur_id_user,
                    v.energie,
                    v.modele,
                    m.libelle AS marque_libelle,
                    (SELECT AVG(note) FROM avis WHERE chauffeur_id = c.chauffeur_id AND statut = "valide") AS chauffeur_note,
                    (c.nb_place - (SELECT COUNT(*) FROM participation p WHERE p.covoiturage_id = c.covoiturage_id AND p.statut_validation NOT IN ("annule"))) AS places_restantes,
                    TIMESTAMPDIFF(MINUTE, CONCAT(c.date_depart, " ", c.heure_depart), CONCAT(c.date_arrivee, " ", c.heure_arrivee)) AS duree_minutes
                FROM covoiturage c
                INNER JOIN utilisateur u ON u.utilisateur_id = c.chauffeur_id
                INNER JOIN voiture v     ON v.voiture_id = c.voiture_id
                INNER JOIN marque m      ON m.marque_id = v.marque_id
                WHERE c.lieu_depart = :depart
                  AND c.lieu_arrivee = :arrivee
                  AND c.date_depart = :date
                  AND c.statut = "prevu"
                HAVING places_restantes >= 1
                ORDER BY c.heure_depart ASC';

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['depart' => $depart, 'arrivee' => $arrivee, 'date' => $date]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve la prochaine date avec un trajet disponible pour un itinéraire donné (US 3).
     */
    public static function findNextAvailableDate(string $depart, string $arrivee, string $afterDate): ?string
    {
        $sql = 'SELECT MIN(c.date_depart) AS prochaine
                FROM covoiturage c
                WHERE c.lieu_depart = :depart
                  AND c.lieu_arrivee = :arrivee
                  AND c.date_depart >= :date
                  AND c.statut = "prevu"
                  AND (c.nb_place - (SELECT COUNT(*) FROM participation p WHERE p.covoiturage_id = c.covoiturage_id AND p.statut_validation NOT IN ("annule"))) >= 1';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['depart' => $depart, 'arrivee' => $arrivee, 'date' => $afterDate]);
        $row = $stmt->fetch();
        return $row['prochaine'] ?? null;
    }

    public static function findById(int $id): ?array
    {
        $sql = 'SELECT
                    c.*,
                    u.pseudo AS chauffeur_pseudo,
                    u.photo  AS chauffeur_photo,
                    u.email  AS chauffeur_email,
                    v.energie, v.modele, v.couleur, v.immatriculation,
                    m.libelle AS marque_libelle,
                    (SELECT AVG(note) FROM avis WHERE chauffeur_id = c.chauffeur_id AND statut = "valide") AS chauffeur_note,
                    (c.nb_place - (SELECT COUNT(*) FROM participation p WHERE p.covoiturage_id = c.covoiturage_id AND p.statut_validation NOT IN ("annule"))) AS places_restantes
                FROM covoiturage c
                INNER JOIN utilisateur u ON u.utilisateur_id = c.chauffeur_id
                INNER JOIN voiture v     ON v.voiture_id = c.voiture_id
                INNER JOIN marque m      ON m.marque_id = v.marque_id
                WHERE c.covoiturage_id = :id';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO covoiturage
                (chauffeur_id, voiture_id, date_depart, heure_depart, date_arrivee, heure_arrivee,
                 lieu_depart, lieu_arrivee, nb_place, prix_personne)
             VALUES
                (:chauffeur_id, :voiture_id, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee,
                 :lieu_depart, :lieu_arrivee, :nb_place, :prix_personne)'
        );
        $stmt->execute($data);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function setStatut(int $id, string $statut): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE covoiturage SET statut = :statut WHERE covoiturage_id = :id'
        );
        $stmt->execute(['statut' => $statut, 'id' => $id]);
    }

    /**
     * Liste les covoiturages d'un chauffeur (avec compteur de participations).
     */
    public static function findByChauffeur(int $userId): array
    {
        $sql = 'SELECT c.*, v.energie, v.modele, m.libelle AS marque_libelle,
                       (SELECT COUNT(*) FROM participation p WHERE p.covoiturage_id = c.covoiturage_id AND p.statut_validation NOT IN ("annule")) AS nb_participants
                FROM covoiturage c
                INNER JOIN voiture v ON v.voiture_id = c.voiture_id
                INNER JOIN marque m  ON m.marque_id = v.marque_id
                WHERE c.chauffeur_id = :id
                ORDER BY c.date_depart DESC, c.heure_depart DESC';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Liste les covoiturages où l'utilisateur est passager.
     */
    public static function findByPassager(int $userId): array
    {
        $sql = 'SELECT c.*, v.energie, m.libelle AS marque_libelle, u.pseudo AS chauffeur_pseudo,
                       p.statut_validation, p.participation_id
                FROM participation p
                INNER JOIN covoiturage c ON c.covoiturage_id = p.covoiturage_id
                INNER JOIN voiture v     ON v.voiture_id = c.voiture_id
                INNER JOIN marque m      ON m.marque_id = v.marque_id
                INNER JOIN utilisateur u ON u.utilisateur_id = c.chauffeur_id
                WHERE p.passager_id = :id
                ORDER BY c.date_depart DESC';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }
}
