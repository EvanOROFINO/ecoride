<div class="container-narrow">
    <h1>Créer un compte</h1>
    <p>Déjà inscrit ? <a href="/login">Se connecter</a></p>

    <form method="post" action="/register" class="card">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" class="form-control"
                   minlength="3" maxlength="50" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control"
                   minlength="8" required>
            <small style="color:var(--color-text-muted)">
                Minimum 8 caractères, avec une majuscule, une minuscule, un chiffre et un caractère spécial.
            </small>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
        </div>

        <p style="background:#E8F5E9;padding:0.75rem 1rem;border-radius:4px;margin:1rem 0">
            <i class="bi bi-gift-fill"></i>
            <strong>20 crédits offerts</strong> à la création de votre compte !
        </p>

        <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
    </form>
</div>
