<?php
/** @var array $preferences */
$fumeur  = $preferences['fumeur']  ?? 'non';
$animaux = $preferences['animaux'] ?? 'non';
$custom = array_diff_key($preferences, ['fumeur' => 1, 'animaux' => 1]);
?>

<div class="container-narrow">
    <a href="/mon-espace" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Mes préférences chauffeur</h1>
    <p style="color:var(--color-text-muted)">Ces préférences sont visibles par les passagers sur la page détail de vos trajets.</p>

    <form method="post" action="/mon-espace/preferences" class="card">
        <?= csrf_field() ?>

        <h3>Préférences principales</h3>

        <div class="form-group">
            <label class="form-label">Fumeur</label>
            <select name="fumeur" class="form-control">
                <option value="non" <?= $fumeur === 'non' ? 'selected' : '' ?>>Non-fumeur</option>
                <option value="oui" <?= $fumeur === 'oui' ? 'selected' : '' ?>>Fumeur autorisé</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Animaux</label>
            <select name="animaux" class="form-control">
                <option value="non" <?= $animaux === 'non' ? 'selected' : '' ?>>Pas d'animaux</option>
                <option value="oui" <?= $animaux === 'oui' ? 'selected' : '' ?>>Animaux acceptés</option>
            </select>
        </div>

        <hr style="margin:2rem 0">

        <h3>Préférences personnalisées</h3>
        <p style="color:var(--color-text-muted);font-size:0.9rem">Ajoutez vos propres préférences (musique, discussion, climatisation, pauses…)</p>

        <div id="custom-prefs">
            <?php foreach ($custom as $cle => $val): ?>
                <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem">
                    <input type="text" name="pref_cle[]"    class="form-control" value="<?= e($cle) ?>" placeholder="Clé">
                    <input type="text" name="pref_valeur[]" class="form-control" value="<?= e((string) $val) ?>" placeholder="Valeur">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-secondary" onclick="addPref()">
            <i class="bi bi-plus-circle"></i> Ajouter une préférence
        </button>

        <hr style="margin:2rem 0">
        <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
    </form>
</div>

<script>
function addPref() {
    const div = document.createElement('div');
    div.className = 'form-group';
    div.style = 'display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem';
    div.innerHTML = `
        <input type="text" name="pref_cle[]" class="form-control" placeholder="Clé (ex : musique)">
        <input type="text" name="pref_valeur[]" class="form-control" placeholder="Valeur (ex : oui)">
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
    `;
    document.getElementById('custom-prefs').appendChild(div);
}
</script>
