<?php
/**
 * Modèle Stats : agrégations pour le dashboard administrateur (US 13).
 */
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Stats
{
    /**
     * Nombre de covoiturages par jour sur les N derniers jours.
     *
     * @return array<int, array{date: string, total: int}>
     */
    public static function covoituragesParJour(int $jours = 30): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT DATE(date_depart) AS date, COUNT(*) AS total
             FROM covoiturage
             WHERE date_depart >= DATE_SUB(CURDATE(), INTERVAL :jours DAY)
             GROUP BY DATE(date_depart)
             ORDER BY date ASC'
        );
        $stmt->bindValue('jours', $jours, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Crédits gagnés par la plateforme par jour sur les N derniers jours.
     */
    public static function creditsParJour(int $jours = 30): array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT DATE(date_transaction) AS date, SUM(montant) AS total
             FROM credit_plateforme
             WHERE date_transaction >= DATE_SUB(CURDATE(), INTERVAL :jours DAY)
             GROUP BY DATE(date_transaction)
             ORDER BY date ASC'
        );
        $stmt->bindValue('jours', $jours, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function totalCredits(): int
    {
        $stmt = Database::getInstance()->query(
            'SELECT COALESCE(SUM(montant), 0) AS total FROM credit_plateforme'
        );
        return (int) $stmt->fetchColumn();
    }

    public static function totalUtilisateurs(): int
    {
        return (int) Database::getInstance()
            ->query('SELECT COUNT(*) FROM utilisateur')
            ->fetchColumn();
    }

    public static function totalCovoiturages(): int
    {
        return (int) Database::getInstance()
            ->query('SELECT COUNT(*) FROM covoiturage')
            ->fetchColumn();
    }
}
