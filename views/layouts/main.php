<?php /** @var string $content Le contenu de la vue rendue par le controller */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>EcoRide</title>
    <meta name="description" content="<?= isset($pageDescription) ? e($pageDescription) : 'EcoRide, plateforme de covoiturage écologique en France.' ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>

<?php require __DIR__ . '/../partials/navbar.php'; ?>

<main>
    <?php if ($msg = flash('success')): ?>
        <div class="container"><div class="alert alert-success"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="container"><div class="alert alert-error"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('info')): ?>
        <div class="container"><div class="alert alert-info"><?= e($msg) ?></div></div>
    <?php endif; ?>

    <?= $content ?? '' ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/search-live.js') ?>" defer></script>
<script src="<?= asset('js/register-validation.js') ?>" defer></script>
</body>
</html>
