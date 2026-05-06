<?php /** @var array $incidents */ ?>
<div class="container">
    <a href="/employe" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Incidents signalés</h1>

    <?php if (empty($incidents)): ?>
        <div class="card" style="text-align:center">
            <p style="color:var(--color-text-muted)">Aucun incident signalé.</p>
        </div>
    <?php else: ?>
        <?php foreach ($incidents as $i): ?>
            <div class="card" style="margin-bottom:1rem">
                <h3 style="margin-top:0">Trajet n°<?= $i['covoiturage_id'] ?> : <?= e($i['lieu_depart']) ?> → <?= e($i['lieu_arrivee']) ?></h3>
                <p style="color:var(--color-text-muted)">
                    <i class="bi bi-calendar"></i>
                    Du <?= format_date_fr($i['date_depart']) ?> à <?= substr($i['heure_depart'], 0, 5) ?>
                    au <?= format_date_fr($i['date_arrivee']) ?> à <?= substr($i['heure_arrivee'], 0, 5) ?>
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin:1rem 0;padding:1rem;background:#FFF8E1;border-radius:8px">
                    <div>
                        <small style="color:var(--color-text-muted)">CHAUFFEUR</small><br>
                        <strong><?= e($i['chauffeur_pseudo']) ?></strong><br>
                        <a href="mailto:<?= e($i['chauffeur_email']) ?>"><?= e($i['chauffeur_email']) ?></a>
                    </div>
                    <div>
                        <small style="color:var(--color-text-muted)">PASSAGER (signalant)</small><br>
                        <strong><?= e($i['passager_pseudo']) ?></strong><br>
                        <a href="mailto:<?= e($i['passager_email']) ?>"><?= e($i['passager_email']) ?></a>
                    </div>
                </div>

                <div style="background:#FFEBEE;padding:1rem;border-radius:8px;border-left:4px solid #C62828">
                    <strong>Description du problème :</strong><br>
                    <?= nl2br(e($i['commentaire_probleme'] ?? '(aucun commentaire fourni)')) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
