<?php
use App\Core\Auth;
$user = Auth::user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<nav class="navbar">
    <div class="navbar-container">
        <a href="/" class="navbar-brand">
            <i class="bi bi-tree-fill"></i> EcoRide
        </a>
        <button class="navbar-toggle" aria-label="Menu"><i class="bi bi-list"></i></button>
        <ul class="navbar-nav">
            <li><a href="/" class="<?= $path === '/' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="/covoiturages" class="<?= str_starts_with($path, '/covoiturages') ? 'active' : '' ?>">Covoiturages</a></li>
            <li><a href="/contact" class="<?= $path === '/contact' ? 'active' : '' ?>">Contact</a></li>

            <?php if ($user): ?>
                <li><a href="/mon-espace"><i class="bi bi-person-circle"></i> <?= e($user['pseudo']) ?> (<?= $user['credit'] ?> cr.)</a></li>
                <?php if (Auth::hasRole('employe')): ?>
                    <li><a href="/employe">Espace employé</a></li>
                <?php endif; ?>
                <?php if (Auth::hasRole('administrateur')): ?>
                    <li><a href="/admin">Admin</a></li>
                <?php endif; ?>
                <li>
                    <form method="post" action="/logout" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-secondary" style="padding:0.4rem 1rem">Déconnexion</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/login" class="btn-cta">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
