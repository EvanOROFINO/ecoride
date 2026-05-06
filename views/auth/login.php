<div class="container-narrow">
    <h1>Connexion</h1>
    <p>Pas encore de compte ? <a href="/register">Créer un compte</a></p>

    <form method="post" action="/login" class="card">
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($_GET['next'] ?? '/mon-espace') ?>">

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>

    <div class="card" style="margin-top:1.5rem;background:#FFF8E1">
        <p style="margin:0"><strong>Comptes de démonstration</strong> (mot de passe : <code>Password123!</code>)</p>
        <ul style="margin-bottom:0">
            <li>Admin : <code>admin@ecoride.fr</code></li>
            <li>Employé : <code>employe@ecoride.fr</code></li>
            <li>Chauffeur : <code>sophie@example.com</code></li>
            <li>Passager : <code>tom@example.com</code></li>
        </ul>
    </div>
</div>
