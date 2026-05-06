<?php /** Page d'accueil — US 1 */ ?>

<section class="hero">
    <h1>Voyagez ensemble, polluez moins</h1>
    <p>
        EcoRide connecte les voyageurs soucieux de l'environnement.<br>
        Trouvez un covoiturage écologique en quelques clics.
    </p>

    <form class="search-bar" action="/covoiturages" method="GET">
        <div>
            <input type="text" class="form-control" name="depart"
                   placeholder="Ville de départ" required value="<?= e($_GET['depart'] ?? '') ?>">
        </div>
        <div>
            <input type="text" class="form-control" name="arrivee"
                   placeholder="Ville d'arrivée" required value="<?= e($_GET['arrivee'] ?? '') ?>">
        </div>
        <div>
            <input type="date" class="form-control" name="date" required
                   data-min-today value="<?= e($_GET['date'] ?? date('Y-m-d')) ?>">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Rechercher
        </button>
    </form>
</section>

<section class="container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:2rem;margin:3rem 0">
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;color:var(--color-primary)"><i class="bi bi-tree-fill"></i></div>
            <h3>100% écologique</h3>
            <p>Identifiez en un coup d'œil les voyages effectués en voiture électrique grâce à notre badge écologique.</p>
        </div>
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;color:var(--color-primary)"><i class="bi bi-piggy-bank-fill"></i></div>
            <h3>Économique</h3>
            <p>Partagez les frais de carburant et bénéficiez de tarifs imbattables sur tous vos trajets.</p>
        </div>
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;color:var(--color-primary)"><i class="bi bi-shield-check"></i></div>
            <h3>Sécurisé</h3>
            <p>Tous nos chauffeurs sont vérifiés et notés par la communauté EcoRide.</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center;margin:4rem 0">
        <div>
            <h2>Notre mission : réduire l'empreinte carbone des trajets</h2>
            <p style="font-size:1.1rem;line-height:1.8">
                EcoRide a été créée en France avec une ambition claire : devenir la principale
                plateforme de covoiturage pour les voyageurs <strong>soucieux de l'environnement</strong>.
            </p>
            <p style="font-size:1.1rem;line-height:1.8">
                En partageant un véhicule, vous divisez les émissions de CO₂, vous économisez sur le carburant,
                et vous contribuez à <strong>désengorger les routes</strong>. Avec EcoRide, chaque kilomètre
                devient un geste pour la planète.
            </p>
            <a href="/covoiturages" class="btn btn-primary" style="margin-top:1rem">Voir tous les trajets</a>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80"
                 alt="Voiture sur une route forestière"
                 style="width:100%;border-radius:var(--radius-md);box-shadow:var(--shadow-card)">
        </div>
    </div>

    <div style="background:var(--color-bg-card);padding:3rem;border-radius:var(--radius-md);text-align:center;margin:4rem 0">
        <h2>Comment ça marche ?</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:2rem;margin-top:2rem">
            <div>
                <div style="font-size:2.5rem;color:var(--color-primary-light);font-weight:700">1</div>
                <h4>Recherchez</h4>
                <p style="color:var(--color-text-muted)">Indiquez votre ville de départ, d'arrivée et la date de votre voyage.</p>
            </div>
            <div>
                <div style="font-size:2.5rem;color:var(--color-primary-light);font-weight:700">2</div>
                <h4>Choisissez</h4>
                <p style="color:var(--color-text-muted)">Comparez les chauffeurs, les notes, les prix et l'aspect écologique des trajets.</p>
            </div>
            <div>
                <div style="font-size:2.5rem;color:var(--color-primary-light);font-weight:700">3</div>
                <h4>Voyagez</h4>
                <p style="color:var(--color-text-muted)">Réservez votre place avec vos crédits et profitez du voyage !</p>
            </div>
        </div>
    </div>
</section>
