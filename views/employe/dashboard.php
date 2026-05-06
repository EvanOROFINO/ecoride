<?php
/** @var int $nbAvis */
/** @var int $nbIncidents */
?>
<div class="container">
    <h1>Espace employé</h1>
    <p style="color:var(--color-text-muted)">Modération et résolution d'incidents</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1.5rem;margin-top:2rem">
        <a href="/employe/avis" class="card" style="text-decoration:none;color:inherit">
            <h2 style="margin-top:0">
                <i class="bi bi-chat-quote-fill" style="color:var(--color-primary)"></i>
                Avis à modérer
            </h2>
            <p style="font-size:3rem;font-weight:700;margin:0;color:var(--color-primary)"><?= $nbAvis ?></p>
            <p style="color:var(--color-text-muted);margin-bottom:0">en attente de validation</p>
        </a>

        <a href="/employe/incidents" class="card" style="text-decoration:none;color:inherit">
            <h2 style="margin-top:0">
                <i class="bi bi-exclamation-triangle-fill" style="color:var(--color-error)"></i>
                Incidents
            </h2>
            <p style="font-size:3rem;font-weight:700;margin:0;color:var(--color-error)"><?= $nbIncidents ?></p>
            <p style="color:var(--color-text-muted);margin-bottom:0">trajets signalés</p>
        </a>
    </div>
</div>
