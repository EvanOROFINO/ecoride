<?php
/** @var int $totalCredits */
/** @var int $totalUtilisateurs */
/** @var int $totalCovoiturages */
?>
<div class="container">
    <h1>Tableau de bord administrateur</h1>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;margin:2rem 0">
        <div class="card" style="text-align:center">
            <small style="color:var(--color-text-muted)">CRÉDITS GAGNÉS</small>
            <div style="font-size:2.5rem;font-weight:700;color:var(--color-primary)"><?= number_format($totalCredits, 0, ',', ' ') ?></div>
        </div>
        <div class="card" style="text-align:center">
            <small style="color:var(--color-text-muted)">UTILISATEURS</small>
            <div style="font-size:2.5rem;font-weight:700"><?= $totalUtilisateurs ?></div>
        </div>
        <div class="card" style="text-align:center">
            <small style="color:var(--color-text-muted)">COVOITURAGES</small>
            <div style="font-size:2.5rem;font-weight:700"><?= $totalCovoiturages ?></div>
        </div>
    </div>

    <div class="card">
        <h2>Activité de la plateforme — 30 derniers jours</h2>
        <div style="height:350px"><canvas id="chart-covoiturages"></canvas></div>
    </div>

    <div class="card" style="margin-top:1rem">
        <h2>Crédits gagnés par jour — 30 derniers jours</h2>
        <div style="height:350px"><canvas id="chart-credits"></canvas></div>
    </div>

    <div class="card" style="margin-top:1rem">
        <h2>Actions</h2>
        <div style="display:flex;gap:1rem;flex-wrap:wrap">
            <a href="/admin/employes" class="btn btn-primary">
                <i class="bi bi-people-fill"></i> Gérer les comptes
            </a>
            <a href="/employe/avis" class="btn btn-secondary">
                <i class="bi bi-chat-quote"></i> Avis à modérer
            </a>
            <a href="/employe/incidents" class="btn btn-secondary">
                <i class="bi bi-exclamation-triangle"></i> Incidents
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
fetch('/admin/api/stats').then(r => r.json()).then(data => {
    const labelsCov = data.covoituragesParJour.map(d => d.date);
    const valuesCov = data.covoituragesParJour.map(d => parseInt(d.total));
    new Chart(document.getElementById('chart-covoiturages'), {
        type: 'line',
        data: {
            labels: labelsCov,
            datasets: [{
                label: 'Covoiturages par jour',
                data: valuesCov,
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    const labelsCred = data.creditsParJour.map(d => d.date);
    const valuesCred = data.creditsParJour.map(d => parseInt(d.total));
    new Chart(document.getElementById('chart-credits'), {
        type: 'bar',
        data: {
            labels: labelsCred,
            datasets: [{
                label: 'Crédits par jour',
                data: valuesCred,
                backgroundColor: '#F9A825',
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
});
</script>
