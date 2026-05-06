<?php
/** @var array $voitures */
/** @var array $marques */
?>

<div class="container">
    <a href="/mon-espace" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Mes véhicules</h1>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:2rem">
        <div>
            <?php if (empty($voitures)): ?>
                <div class="card">
                    <p>Vous n'avez encore ajouté aucun véhicule. Ajoutez-en un pour pouvoir proposer des trajets.</p>
                </div>
            <?php else: ?>
                <?php foreach ($voitures as $v): ?>
                    <div class="card" style="margin-bottom:1rem">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
                            <div>
                                <h3 style="margin:0"><?= e($v['marque_libelle']) ?> <?= e($v['modele']) ?></h3>
                                <p style="margin:0.25rem 0;color:var(--color-text-muted)">
                                    <?= e($v['couleur']) ?> · <?= e($v['immatriculation']) ?> · <?= e($v['nb_places']) ?> places
                                </p>
                                <?php if ($v['energie'] === 'electrique'): ?>
                                    <span class="badge badge-eco">Électrique</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= e(ucfirst($v['energie'])) ?></span>
                                <?php endif; ?>
                            </div>
                            <form method="post" action="/mon-espace/vehicules/<?= $v['voiture_id'] ?>/supprimer">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger" data-confirm="Supprimer ce véhicule ?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <aside class="card" style="height:fit-content">
            <h3 style="margin-top:0">Ajouter un véhicule</h3>
            <form method="post" action="/mon-espace/vehicules">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label">Marque (existante)</label>
                    <select name="marque_id" class="form-control">
                        <option value="">— Choisir —</option>
                        <?php foreach ($marques as $m): ?>
                            <option value="<?= $m['marque_id'] ?>"><?= e($m['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ou nouvelle marque</label>
                    <input type="text" name="marque_nouvelle" class="form-control" placeholder="Ex : Skoda">
                </div>

                <div class="form-group">
                    <label class="form-label">Modèle</label>
                    <input type="text" name="modele" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Immatriculation</label>
                    <input type="text" name="immatriculation" class="form-control" required maxlength="15" style="text-transform:uppercase">
                </div>

                <div class="form-group">
                    <label class="form-label">Énergie</label>
                    <select name="energie" class="form-control" required>
                        <option value="essence">Essence</option>
                        <option value="diesel">Diesel</option>
                        <option value="electrique">Électrique</option>
                        <option value="hybride">Hybride</option>
                        <option value="gpl">GPL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Couleur</label>
                    <input type="text" name="couleur" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">1ère immatriculation</label>
                    <input type="date" name="date_premiere_immatriculation" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de places</label>
                    <input type="number" name="nb_places" class="form-control" min="1" max="9" value="4" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
            </form>
        </aside>
    </div>
</div>
