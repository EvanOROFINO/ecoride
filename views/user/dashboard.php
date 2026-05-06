<?php
/** @var array $user */
/** @var array $voyages */
/** @var array $voyagesChauffeur */
$roles = $user['roles'] ?? [];
?>

<div class="container">
    <h1>Bienvenue, <?= e($user['pseudo']) ?> !</h1>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;margin:2rem 0">
        <div class="card" style="text-align:center">
            <small style="color:var(--color-text-muted)">CRÉDITS DISPONIBLES</small>
            <div style="font-size:2.5rem;font-weight:700;color:var(--color-primary)">
                <?= $user['credit'] ?>
            </div>
        </div>
        <div class="card" style="text-align:center">
            <small style="color:var(--color-text-muted)">TRAJETS PASSAGER</small>
            <div style="font-size:2.5rem;font-weight:700"><?= count($voyages) ?></div>
        </div>
        <?php if (in_array('chauffeur', $roles, true)): ?>
            <div class="card" style="text-align:center">
                <small style="color:var(--color-text-muted)">TRAJETS CHAUFFEUR</small>
                <div style="font-size:2.5rem;font-weight:700"><?= count($voyagesChauffeur) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Quel est votre rôle ?</h2>
        <p style="color:var(--color-text-muted)">Sélectionnez votre/vos rôle(s) sur la plateforme. Un chauffeur doit ajouter au moins un véhicule.</p>
        <form method="post" action="/mon-espace/role">
            <?= csrf_field() ?>
            <label style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border:2px solid #eee;border-radius:8px;margin-bottom:0.5rem;cursor:pointer">
                <input type="checkbox" name="role_chauffeur" value="1" <?= in_array('chauffeur', $roles, true) ? 'checked' : '' ?>>
                <span><strong>Chauffeur</strong> — je propose des trajets et accepte des passagers</span>
            </label>
            <label style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border:2px solid #eee;border-radius:8px;margin-bottom:0.5rem;cursor:pointer">
                <input type="checkbox" name="role_passager" value="1" <?= in_array('passager', $roles, true) ? 'checked' : '' ?>>
                <span><strong>Passager</strong> — je rejoins les trajets proposés</span>
            </label>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem;margin-top:1rem">
        <a href="/mon-espace/vehicules" class="card" style="text-decoration:none;color:inherit">
            <h3 style="margin-top:0"><i class="bi bi-car-front-fill"></i> Mes véhicules</h3>
            <p style="color:var(--color-text-muted);margin-bottom:0">Gérer mes voitures (chauffeur).</p>
        </a>
        <a href="/mon-espace/preferences" class="card" style="text-decoration:none;color:inherit">
            <h3 style="margin-top:0"><i class="bi bi-stars"></i> Mes préférences</h3>
            <p style="color:var(--color-text-muted);margin-bottom:0">Fumeur, animaux, musique...</p>
        </a>
        <?php if (in_array('chauffeur', $roles, true)): ?>
            <a href="/mon-espace/voyages/nouveau" class="card" style="text-decoration:none;color:inherit;background:#E8F5E9">
                <h3 style="margin-top:0"><i class="bi bi-plus-circle-fill"></i> Proposer un voyage</h3>
                <p style="color:var(--color-text-muted);margin-bottom:0">Créer un nouveau covoiturage.</p>
            </a>
        <?php endif; ?>
        <a href="/mon-espace/historique" class="card" style="text-decoration:none;color:inherit">
            <h3 style="margin-top:0"><i class="bi bi-clock-history"></i> Mon historique</h3>
            <p style="color:var(--color-text-muted);margin-bottom:0">Tous mes covoiturages.</p>
        </a>
    </div>
</div>
