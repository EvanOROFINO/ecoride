<?php
/**
 * Service métier — Participation à un covoiturage.
 *
 * Orchestre la logique transactionnelle : vérification places, débit crédits passager,
 * inscription, mise à jour session. Le controller ne fait plus que de l'HTTP plumbing.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\CovoiturageRepository;
use App\Repositories\ParticipationRepository;
use App\Repositories\UserRepository;
use DomainException;

final class CovoiturageService
{
    /**
     * Inscrit un passager à un covoiturage en respectant toutes les règles métier.
     *
     * @throws DomainException Si une règle métier est violée (place, crédit, doublon, etc.)
     */
    public function participer(int $covoiturageId, int $userId, int $userCredit): int
    {
        $covoiturage = CovoiturageRepository::findById($covoiturageId);
        if (!$covoiturage) {
            throw new DomainException('Covoiturage introuvable.');
        }
        if ((int) $covoiturage['chauffeur_id'] === $userId) {
            throw new DomainException('Vous ne pouvez pas participer à votre propre trajet.');
        }
        if (ParticipationRepository::exists($covoiturageId, $userId)) {
            throw new DomainException('Vous êtes déjà inscrit à ce trajet.');
        }
        if ((int) $covoiturage['places_restantes'] < 1) {
            throw new DomainException('Plus de place disponible.');
        }

        $prix = (int) $covoiturage['prix_personne'];
        if ($userCredit < $prix) {
            throw new DomainException('Crédit insuffisant.');
        }

        // Transaction ACID — verrou applicatif pour éviter race conditions sur les places
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                'SELECT (c.nb_place - (SELECT COUNT(*) FROM participation p
                    WHERE p.covoiturage_id = c.covoiturage_id
                    AND p.statut_validation != "annule")) AS places
                 FROM covoiturage c WHERE c.covoiturage_id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $covoiturageId]);
            $places = (int) $stmt->fetchColumn();
            if ($places < 1) {
                throw new DomainException('Plus de place disponible.');
            }

            UserRepository::updateCredit($userId, -$prix);
            ParticipationRepository::create($covoiturageId, $userId);

            $db->commit();
            return $prix;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Crée un trajet (chauffeur uniquement). Validation des règles métier.
     */
    public function creerTrajet(array $data, int $chauffeurId, array $voiture): int
    {
        $prix = (float) ($data['prix_personne'] ?? 0);
        if ($prix < 3) {
            throw new DomainException('Le prix doit être au minimum de 3 crédits (dont 2 prélevés par la plateforme).');
        }

        if ((int) $voiture['utilisateur_id'] !== $chauffeurId) {
            throw new DomainException('Véhicule invalide.');
        }

        return CovoiturageRepository::create([
            'chauffeur_id'  => $chauffeurId,
            'voiture_id'    => (int) $voiture['voiture_id'],
            'date_depart'   => $data['date_depart'],
            'heure_depart'  => $data['heure_depart'],
            'date_arrivee'  => $data['date_arrivee'],
            'heure_arrivee' => $data['heure_arrivee'],
            'lieu_depart'   => $data['lieu_depart'],
            'lieu_arrivee'  => $data['lieu_arrivee'],
            'nb_place'      => min((int) $voiture['nb_places'], (int) ($data['nb_place'] ?? 4)),
            'prix_personne' => $prix,
        ]);
    }
}
