<?php
/** @var array $voyagesChauffeur */
/** @var array $voyagesPassager */
?>

<div class="container">
    <a href="/mon-espace" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Mon historique</h1>

    <?php if (!empty($voyagesChauffeur)): ?>
        <h2>En tant que chauffeur</h2>
        <?php foreach ($voyagesChauffeur as $c): ?>
            <div class="card" style="margin-bottom:1rem">
                <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem">
                    <div>
                        <h3 style="margin:0"><?= e($c['lieu_depart']) ?> → <?= e($c['lieu_arrivee']) ?></h3>
                        <p style="margin:0.25rem 0;color:var(--color-text-muted)">
                            <i class="bi bi-calendar"></i> <?= format_date_fr($c['date_depart']) ?> à <?= substr($c['heure_depart'], 0, 5) ?>
                            · <i class="bi bi-people"></i> <?= $c['nb_participants'] ?>/<?= $c['nb_place'] ?> places
                            · <span class="badge badge-info"><?= e(ucfirst($c['statut'])) ?></span>
                            <?php if ($c['energie'] === 'electrique'): ?>
                                <span class="badge badge-eco">Éco</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                        <a href="/covoiturages/<?= $c['covoiturage_id'] ?>" class="btn btn-secondary">Voir</a>

                        <?php if ($c['statut'] === 'prevu'): ?>
                            <form method="post" action="/mon-espace/voyages/<?= $c['covoiturage_id'] ?>/demarrer" style="display:inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-primary">Démarrer</button>
                            </form>
                            <form method="post" action="/mon-espace/voyages/<?= $c['covoiturage_id'] ?>/annuler" style="display:inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger" data-confirm="Annuler ce trajet ? Les passagers seront remboursés.">Annuler</button>
                            </form>
                        <?php elseif ($c['statut'] === 'en_cours'): ?>
                            <form method="post" action="/mon-espace/voyages/<?= $c['covoiturage_id'] ?>/arriver" style="display:inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-primary">Arrivée à destination</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($voyagesPassager)): ?>
        <h2 style="margin-top:2rem">En tant que passager</h2>
        <?php foreach ($voyagesPassager as $c): ?>
            <div class="card" style="margin-bottom:1rem">
                <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem">
                    <div>
                        <h3 style="margin:0"><?= e($c['lieu_depart']) ?> → <?= e($c['lieu_arrivee']) ?></h3>
                        <p style="margin:0.25rem 0;color:var(--color-text-muted)">
                            <i class="bi bi-calendar"></i> <?= format_date_fr($c['date_depart']) ?> à <?= substr($c['heure_depart'], 0, 5) ?>
                            · Chauffeur : <strong><?= e($c['chauffeur_pseudo']) ?></strong>
                            · <span class="badge badge-info"><?= e(ucfirst($c['statut'])) ?></span>
                        </p>
                        <p style="margin:0;color:var(--color-text-muted);font-size:0.9rem">
                            Statut validation : <?= e(ucfirst(str_replace('_', ' ', $c['statut_validation']))) ?>
                        </p>
                    </div>
                    <div style="display:flex;gap:0.5rem">
                        <a href="/covoiturages/<?= $c['covoiturage_id'] ?>" class="btn btn-secondary">Voir</a>

                        <?php if ($c['statut'] === 'termine' && $c['statut_validation'] === 'en_attente'): ?>
                            <button class="btn btn-primary" onclick="document.getElementById('valid-<?= $c['participation_id'] ?>').showModal()">
                                Valider le trajet
                            </button>
                        <?php elseif ($c['statut'] === 'prevu' && $c['statut_validation'] === 'en_attente'): ?>
                            <form method="post" action="/mon-espace/voyages/<?= $c['covoiturage_id'] ?>/annuler" style="display:inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger" data-confirm="Annuler votre participation ? Vos crédits seront restitués.">Se désinscrire</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($c['statut'] === 'termine' && $c['statut_validation'] === 'en_attente'): ?>
                <dialog id="valid-<?= $c['participation_id'] ?>" style="border:none;border-radius:8px;padding:0;max-width:500px;width:90%">
                    <form method="post" action="/mon-espace/participations/<?= $c['participation_id'] ?>/valider" class="card" style="margin:0">
                        <?= csrf_field() ?>
                        <h3 style="margin-top:0">Comment s'est passé le trajet ?</h3>

                        <div class="form-group">
                            <label class="form-label">Tout s'est bien passé ?</label>
                            <select name="action" class="form-control" id="action-<?= $c['participation_id'] ?>" onchange="document.getElementById('rating-<?= $c['participation_id'] ?>').style.display = this.value === 'ok' ? 'block' : 'none'">
                                <option value="ok">Oui, tout s'est bien passé</option>
                                <option value="probleme">Non, il y a eu un problème</option>
                            </select>
                        </div>

                        <div id="rating-<?= $c['participation_id'] ?>">
                            <div class="form-group">
                                <label class="form-label">Note (1 à 5 étoiles)</label>
                                <select name="note" class="form-control">
                                    <option value="5">★★★★★ — Excellent</option>
                                    <option value="4">★★★★☆ — Très bien</option>
                                    <option value="3">★★★☆☆ — Correct</option>
                                    <option value="2">★★☆☆☆ — Décevant</option>
                                    <option value="1">★☆☆☆☆ — Mauvais</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Commentaire</label>
                            <textarea name="commentaire" class="form-control" rows="4"></textarea>
                        </div>

                        <div style="display:flex;gap:0.5rem">
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                            <button type="button" class="btn btn-secondary" onclick="this.closest('dialog').close()">Annuler</button>
                        </div>
                    </form>
                </dialog>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($voyagesChauffeur) && empty($voyagesPassager)): ?>
        <div class="card" style="text-align:center">
            <p>Aucun voyage pour le moment.</p>
            <a href="/covoiturages" class="btn btn-primary">Trouver un trajet</a>
        </div>
    <?php endif; ?>
</div>
