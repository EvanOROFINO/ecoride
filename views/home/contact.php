<div class="container-narrow">
    <h1>Contact</h1>
    <p>Une question, un problème, une suggestion ? Écrivez-nous.</p>

    <form method="post" action="/contact" class="card">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="nom">Votre nom</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="sujet">Sujet</label>
            <input type="text" id="sujet" name="sujet" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="6" minlength="10" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>

    <div class="card" style="margin-top:1.5rem;background:#E3F2FD">
        <p style="margin:0">
            <i class="bi bi-envelope-fill"></i>
            Vous pouvez aussi nous écrire directement à
            <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a>
        </p>
    </div>
</div>
