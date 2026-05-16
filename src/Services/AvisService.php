<?php
/**
 * Service métier — Modération des avis (US 11 et 12).
 *
 * Centralise la logique de création (validation note + commentaire) et de modération
 * (validation/refus par un employé). Les avis sont stockés en MongoDB.
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AvisRepository;
use DomainException;

final class AvisService
{
    /**
     * Crée un nouvel avis client (note 1-5 + commentaire) en attente de modération.
     *
     * @throws DomainException Si la note est invalide ou le commentaire trop court.
     */
    public function creerAvis(array $data): string
    {
        $note = (int) ($data['note'] ?? 0);
        $commentaire = trim((string) ($data['commentaire'] ?? ''));

        if ($note < 1 || $note > 5) {
            throw new DomainException('La note doit être comprise entre 1 et 5.');
        }
        if (mb_strlen($commentaire) < 5) {
            throw new DomainException('Le commentaire doit contenir au moins 5 caractères.');
        }

        $data['commentaire'] = $commentaire;
        return AvisRepository::create($data);
    }

    public function moderer(string $avisId, bool $accepter, int $employeId): void
    {
        if ($accepter) {
            AvisRepository::valider($avisId, $employeId);
        } else {
            AvisRepository::refuser($avisId, $employeId);
        }
    }

    public function listerEnAttente(): array
    {
        return AvisRepository::findEnAttente();
    }
}
