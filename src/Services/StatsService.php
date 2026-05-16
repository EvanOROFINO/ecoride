<?php
/**
 * Service métier — Agrégations statistiques pour le dashboard administrateur (US 13).
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StatsRepository;

final class StatsService
{
    /**
     * Retourne toutes les données nécessaires au dashboard admin.
     */
    public function dashboard(int $jours = 30): array
    {
        return [
            'covoituragesParJour' => StatsRepository::covoituragesParJour($jours),
            'creditsParJour'      => StatsRepository::creditsParJour($jours),
            'totalCredits'        => StatsRepository::totalCredits(),
            'totalUtilisateurs'   => StatsRepository::totalUtilisateurs(),
            'totalCovoiturages'   => StatsRepository::totalCovoiturages(),
            'jours'               => $jours,
        ];
    }
}
