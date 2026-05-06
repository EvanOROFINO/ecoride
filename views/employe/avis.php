<?php /** @var array $avis */ ?>
<div class="container">
    <a href="/employe" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Avis en attente de modération</h1>

    <?php if (empty($avis)): ?>
        <div class="card" style="text-align:center">
            <p style="color:var(--color-text-muted)">Aucun avis à modérer pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($avis as $a):
            $id = (string) $a['_id'];
            $note = (int) ($a['note'] ?? 0);
        ?>
            <div class="card" style="margin-bottom:1rem">
                <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem">
                    <div style="flex:1;min-width:280px">
                        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem">
                            <strong><?= e((string) ($a['auteur_pseudo'] ?? 'Anonyme')) ?></strong>
                            <span style="color:var(--color-text-muted)">→</span>
                            <span>Chauffeur <strong>#<?= e((string) ($a['chauffeur_id'] ?? '?')) ?></strong></span>
                            <span class="rating">
                                <?= str_repeat('★', $note) . str_repeat('<span class="empty">★</span>', 5 - $note) ?>
                            </span>
                        </div>
                        <p style="margin:0;color:var(--color-text-muted)">
                            « <?= e((string) ($a['commentaire'] ?? '')) ?> »
                        </p>
                    </div>
                    <div style="display:flex;gap:0.5rem">
                        <form method="post" action="/employe/avis/<?= $id ?>/valider" style="display:inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Valider</button>
                        </form>
                        <form method="post" action="/employe/avis/<?= $id ?>/refuser" style="display:inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger" data-confirm="Refuser cet avis ?"><i class="bi bi-x-circle"></i> Refuser</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
