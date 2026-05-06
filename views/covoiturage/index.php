<?php
/** @var bool $hasSearch */
/** @var array $covoiturages */
/** @var ?string $prochaineDate */
/** @var array $filters */
?>

<section style="background:linear-gradient(135deg, #2E7D32 0%, #66BB6A 100%);color:white;padding:2rem 1.5rem">
    <div class="container" style="padding-top:1rem;padding-bottom:1rem">
        <h1 style="color:white;margin-bottom:1rem">Rechercher un covoiturage</h1>

        <form action="/covoiturages" method="GET" class="search-bar" style="margin:0">
            <input type="text" class="form-control" name="depart"
                   placeholder="Ville de départ" required value="<?= e($depart ?? '') ?>">
            <input type="text" class="form-control" name="arrivee"
                   placeholder="Ville d'arrivée" required value="<?= e($arrivee ?? '') ?>">
            <input type="date" class="form-control" name="date" required
                   data-min-today value="<?= e($date) ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
        </form>
    </div>
</section>

<div class="container">
    <?php if ($hasSearch): ?>
        <div style="display:grid;grid-template-columns:280px 1fr;gap:2rem;margin-top:2rem">

            <aside class="card" style="height:fit-content;position:sticky;top:90px">
                <h3 style="margin-top:0">Filtres</h3>
                <form method="GET" action="/covoiturages">
                    <input type="hidden" name="depart"  value="<?= e($depart) ?>">
                    <input type="hidden" name="arrivee" value="<?= e($arrivee) ?>">
                    <input type="hidden" name="date"    value="<?= e($date) ?>">

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                            <input type="checkbox" name="eco" value="1" <?= $filters['eco'] ? 'checked' : '' ?>>
                            <span class="badge badge-eco">Écologique uniquement</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prix_max">Prix maximum (crédits)</label>
                        <input type="number" id="prix_max" name="prix_max" class="form-control"
                               min="0" step="1" value="<?= $filters['prix_max'] !== null ? (int) $filters['prix_max'] : '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="duree_max">Durée maximum (minutes)</label>
                        <input type="number" id="duree_max" name="duree_max" class="form-control"
                               min="0" step="15" value="<?= $filters['duree_max'] !== null ? $filters['duree_max'] : '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="note_min">Note minimale du chauffeur</label>
                        <select id="note_min" name="note_min" class="form-control">
                            <option value="">Toutes les notes</option>
                            <?php foreach ([5, 4, 3, 2, 1] as $n): ?>
                                <option value="<?= $n ?>" <?= ($filters['note_min'] ?? '') == $n ? 'selected' : '' ?>>
                                    <?= str_repeat('★', $n) ?> et plus
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Appliquer les filtres</button>
                    <a href="/covoiturages?depart=<?= urlencode($depart) ?>&arrivee=<?= urlencode($arrivee) ?>&date=<?= urlencode($date) ?>"
                       class="btn btn-secondary btn-block" style="margin-top:0.5rem">Réinitialiser</a>
                </form>
            </aside>

            <div>
                <p style="color:var(--color-text-muted);margin-bottom:1rem">
                    <strong><?= count($covoiturages) ?></strong> trajet(s) trouvé(s) de
                    <strong><?= e($depart) ?></strong> à <strong><?= e($arrivee) ?></strong>
                    le <?= format_date_fr($date) ?>
                </p>

                <?php if (empty($covoiturages)): ?>
                    <div class="card" style="text-align:center">
                        <p style="font-size:1.1rem;margin-bottom:1.5rem">
                            <i class="bi bi-emoji-frown" style="font-size:2rem;color:var(--color-text-muted)"></i><br>
                            Aucun trajet ne correspond à votre recherche.
                        </p>

                        <?php if ($prochaineDate): ?>
                            <p>Le prochain trajet disponible est le <strong><?= format_date_fr($prochaineDate) ?></strong>.</p>
                            <a href="/covoiturages?depart=<?= urlencode($depart) ?>&arrivee=<?= urlencode($arrivee) ?>&date=<?= urlencode($prochaineDate) ?>"
                               class="btn btn-primary">
                                Voir les trajets du <?= format_date_fr($prochaineDate) ?>
                            </a>
                        <?php else: ?>
                            <p>Aucun autre trajet n'est planifié pour cet itinéraire.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($covoiturages as $c): ?>
                        <div class="card covoiturage-card">
                            <img src="<?= asset('images/' . ($c['chauffeur_photo'] ?: 'default-avatar.png')) ?>"
                                 alt="" class="driver-photo"
                                 onerror="this.src='https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($c['chauffeur_pseudo']) ?>'">

                            <div class="info-grid">
                                <div>
                                    <strong><?= e($c['chauffeur_pseudo']) ?></strong><br>
                                    <span class="rating">
                                        <?php
                                        $note = $c['chauffeur_note'] !== null ? round((float) $c['chauffeur_note']) : 0;
                                        echo str_repeat('★', $note) . str_repeat('<span class="empty">★</span>', 5 - $note);
                                        ?>
                                    </span>
                                </div>
                                <div>
                                    <i class="bi bi-clock"></i>
                                    <strong><?= substr($c['heure_depart'], 0, 5) ?></strong>
                                    →
                                    <strong><?= substr($c['heure_arrivee'], 0, 5) ?></strong>
                                    <small>(<?= format_duree((int) $c['duree_minutes']) ?>)</small>
                                </div>
                                <div>
                                    <i class="bi bi-people-fill"></i>
                                    <?= $c['places_restantes'] ?> place(s) restante(s)
                                </div>
                                <div>
                                    <?php if ($c['energie'] === 'electrique'): ?>
                                        <span class="badge badge-eco">Voyage écologique</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?= e(ucfirst($c['energie'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="text-align:right">
                                <div class="price"><?= number_format((float) $c['prix_personne'], 0) ?> cr.</div>
                                <a href="/covoiturages/<?= $c['covoiturage_id'] ?>" class="btn btn-primary" style="margin-top:0.5rem">
                                    Détails
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="text-align:center;margin-top:2rem">
            <i class="bi bi-search" style="font-size:3rem;color:var(--color-primary)"></i>
            <h2>Lancez votre recherche</h2>
            <p>Indiquez votre ville de départ, d'arrivée et la date de votre voyage pour trouver un covoiturage.</p>
        </div>
    <?php endif; ?>
</div>
