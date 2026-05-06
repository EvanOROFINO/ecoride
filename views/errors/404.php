<?php
ob_start();
require __DIR__ . '/../partials/navbar.php';
?>
<div class="container-narrow" style="text-align:center;padding:5rem 1.5rem">
    <h1 style="font-size:6rem;margin:0;color:#2E7D32">404</h1>
    <h2>Page introuvable</h2>
    <p>La page que vous cherchez n'existe pas ou a été déplacée.</p>
    <a href="/" class="btn btn-primary">Retour à l'accueil</a>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
