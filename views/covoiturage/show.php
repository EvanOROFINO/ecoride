<?php
/** @var array $covoiturage */
/** @var array $avis */
/** @var array $preferences */
/** @var ?float $noteMoyenne */
/** @var bool $canParticipate */
/** @var string $reason */
use App\Core\Auth;
?>

<div class="container">
    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem">
        <div>
            <div class="card">
                <h1 style="margin-top:0"><?= e($covoiturage['lieu_depart']) ?> → <?= e($covoiturage['lieu_arrivee']) ?></h1>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin:1.5rem 0">
                    <div>
                        <small style="color:var(--color-text-muted)">DÉPART</small>
                        <p style="margin:0;font-size:1.1rem">
                            <strong><?= format_date_fr($covoiturage['date_depart']) ?></strong><br>
                            à <?= substr($covoiturage['heure_depart'], 0, 5) ?>
                        </p>
                    </div>
                    <div>
                        <small style="color:var(--color-text-muted)">ARRIVÉE</small>
                        <p style="margin:0;font-size:1.1rem">
                            <strong><?= format_date_fr($covoiturage['date_arrivee']) ?></strong><br>
                            à <?= substr($covoiturage['heure_arrivee'], 0, 5) ?>
                        </p>
                    </div>
                </div>

                <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1rem">
                    <span class="badge badge-info"><i class="bi bi-people-fill"></i> <?= $covoiturage['places_restantes'] ?> place(s) restante(s)</span>
                    <?php if ($covoiturage['energie'] === 'electrique'): ?>
                        <span class="badge badge-eco">Voyage écologique</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card" style="margin-top:1rem">
                <h2><i class="bi bi-car-front-fill"></i> Véhicule</h2>
                <p>
                    <strong><?= e($covoiturage['marque_libelle']) ?> <?= e($covoiturage['modele']) ?></strong>
                    — <?= e($covoiturage['couleur']) ?>
                </p>
                <p>Énergie : <strong><?= e(ucfirst($covoiturage['energie'])) ?></strong></p>
            </div>

            <?php if (!empty($preferences)): ?>
                <div class="card" style="margin-top:1rem">
                    <h2><i class="bi bi-stars"></i> Préférences du chauffeur</h2>
                    <ul style="list-style:none;padding:0;margin:0">
                        <?php foreach ($preferences as $cle => $valeur): ?>
                            <li style="padding:0.5rem 0;border-bottom:1px solid #eee">
                                <strong><?= e(ucfirst((string) $cle)) ?> :</strong> <?= e((string) $valeur) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card" style="margin-top:1rem">
                <h2><i class="bi bi-chat-quote-fill"></i> Avis sur le chauffeur</h2>
                <?php if (empty($avis)): ?>
                    <p style="color:var(--color-text-muted)">Aucun avis pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($avis as $a): ?>
                        <div style="padding:1rem 0;border-bottom:1px solid #eee">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                                <strong><?= e((string) ($a['auteur_pseudo'] ?? 'Anonyme')) ?></strong>
                                <span class="rating">
                                    <?php
                                    $n = (int) ($a['note'] ?? 0);
                                    echo str_repeat('★', $n) . str_repeat('<span class="empty">★</span>', 5 - $n);
                                    ?>
                                </span>
                            </div>
                            <p style="margin:0;color:var(--color-text-muted)"><?= e((string) ($a['commentaire'] ?? '')) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <aside>
            <div class="card" style="position:sticky;top:90px">
                <div style="text-align:center">
                    <img src="<?= asset('images/' . ($covoiturage['chauffeur_photo'] ?? 'default-avatar.png')) ?>"
                         alt="" class="driver-photo"
                         style="width:96px;height:96px"
                         onerror="this.src='https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($covoiturage['chauffeur_pseudo']) ?>'">
                    <h3 style="margin:0.5rem 0 0"><?= e($covoiturage['chauffeur_pseudo']) ?></h3>
                    <?php if ($noteMoyenne !== null): ?>
                        <div class="rating" style="font-size:1.2rem">
                            <?php
                            $n = (int) round($noteMoyenne);
                            echo str_repeat('★', $n) . str_repeat('<span class="empty">★</span>', 5 - $n);
                            ?>
                            <small style="color:var(--color-text-muted)">(<?= $noteMoyenne ?>/5)</small>
                        </div>
                    <?php endif; ?>
                </div>

                <hr style="margin:1.5rem 0">

                <div style="text-align:center">
                    <div class="price" style="font-size:2.5rem;color:var(--color-primary);font-weight:700">
                        <?= number_format((float) $covoiturage['prix_personne'], 0) ?>
                    </div>
                    <small style="color:var(--color-text-muted)">crédits / personne</small>
                </div>

                <hr style="margin:1.5rem 0">

                <?php if (!Auth::check()): ?>
                    <a href="/login?next=<?= urlencode("/covoiturages/{$covoiturage['covoiturage_id']}") ?>"
                       class="btn btn-primary btn-block">Se connecter pour participer</a>
                    <p style="text-align:center;margin-top:0.75rem;color:var(--color-text-muted);font-size:0.9rem">
                        Pas encore de compte ? <a href="/register">S'inscrire</a>
                    </p>
                <?php elseif ($canParticipate): ?>
                    <form method="post" action="/covoiturages/<?= $covoiturage['covoiturage_id'] ?>/participer"
                          onsubmit="return confirmParticipation(event)">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-block">
                            Participer pour <?= number_format((float) $covoiturage['prix_personne'], 0) ?> crédits
                        </button>
                    </form>
                    <script>
                        function confirmParticipation(e) {
                            const prix = <?= (float) $covoiturage['prix_personne'] ?>;
                            const ok1 = confirm(`Confirmer la participation à ce trajet ? ${prix} crédits seront débités.`);
                            if (!ok1) { e.preventDefault(); return false; }
                            const ok2 = confirm(`Êtes-vous absolument sûr ? Cette action est définitive.`);
                            if (!ok2) { e.preventDefault(); return false; }
                            return true;
                        }
                    </script>
                <?php else: ?>
                    <button class="btn btn-secondary btn-block" disabled>
                        <i class="bi bi-info-circle"></i> Indisponible
                    </button>
                    <p style="text-align:center;margin-top:0.75rem;color:var(--color-error);font-size:0.9rem">
                        <?= e($reason) ?>
                    </p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
