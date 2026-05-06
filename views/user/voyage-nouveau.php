<?php /** @var array $voitures */ ?>

<div class="container-narrow">
    <a href="/mon-espace" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Proposer un voyage</h1>

    <?php if (empty($voitures)): ?>
        <div class="alert alert-info">
            Vous devez d'abord <a href="/mon-espace/vehicules">ajouter un véhicule</a> pour proposer un trajet.
        </div>
    <?php else: ?>

        <form method="post" action="/mon-espace/voyages/nouveau" class="card">
            <?= csrf_field() ?>

            <h3>Itinéraire</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label class="form-label">Lieu de départ</label>
                    <input type="text" name="lieu_depart" class="form-control" required placeholder="Paris">
                </div>
                <div class="form-group">
                    <label class="form-label">Lieu d'arrivée</label>
                    <input type="text" name="lieu_arrivee" class="form-control" required placeholder="Lyon">
                </div>
            </div>

            <h3>Horaires</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label class="form-label">Date de départ</label>
                    <input type="date" name="date_depart" class="form-control" required data-min-today>
                </div>
                <div class="form-group">
                    <label class="form-label">Heure de départ</label>
                    <input type="time" name="heure_depart" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date d'arrivée</label>
                    <input type="date" name="date_arrivee" class="form-control" required data-min-today>
                </div>
                <div class="form-group">
                    <label class="form-label">Heure d'arrivée</label>
                    <input type="time" name="heure_arrivee" class="form-control" required>
                </div>
            </div>

            <h3>Véhicule</h3>
            <div class="form-group">
                <label class="form-label">Véhicule utilisé</label>
                <select name="voiture_id" class="form-control" required>
                    <?php foreach ($voitures as $v): ?>
                        <option value="<?= $v['voiture_id'] ?>">
                            <?= e($v['marque_libelle']) ?> <?= e($v['modele']) ?>
                            (<?= e($v['immatriculation']) ?>) — <?= e(ucfirst($v['energie'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small><a href="/mon-espace/vehicules">Ajouter un nouveau véhicule</a></small>
            </div>

            <h3>Tarif et places</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label class="form-label">Nombre de places à proposer</label>
                    <input type="number" name="nb_place" class="form-control" min="1" max="8" value="3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Prix par personne (crédits)</label>
                    <input type="number" name="prix_personne" class="form-control" min="3" step="1" value="20" required>
                    <small style="color:var(--color-text-muted)">2 crédits sont prélevés par la plateforme.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Publier le trajet</button>
        </form>

    <?php endif; ?>
</div>
