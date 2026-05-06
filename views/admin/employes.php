<?php /** @var array $users */ ?>
<div class="container">
    <a href="/admin" class="btn btn-secondary" style="margin-bottom:1rem">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1>Gestion des comptes</h1>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:2rem">
        <div>
            <table class="card" style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left;border-bottom:2px solid var(--color-border)">
                        <th style="padding:0.75rem 0">Pseudo</th>
                        <th style="padding:0.75rem 0">Email</th>
                        <th style="padding:0.75rem 0">Rôles</th>
                        <th style="padding:0.75rem 0">Statut</th>
                        <th style="padding:0.75rem 0">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom:1px solid var(--color-border)">
                            <td style="padding:0.75rem 0"><strong><?= e($u['pseudo']) ?></strong></td>
                            <td style="padding:0.75rem 0;color:var(--color-text-muted);font-size:0.9rem"><?= e($u['email']) ?></td>
                            <td style="padding:0.75rem 0">
                                <?php foreach (explode(',', $u['roles'] ?? '') as $role): ?>
                                    <span class="badge badge-info" style="margin-right:0.25rem"><?= e($role) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td style="padding:0.75rem 0">
                                <?php if ($u['statut'] === 'actif'): ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Suspendu</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem 0">
                                <?php if ($u['statut'] === 'actif'): ?>
                                    <form method="post" action="/admin/utilisateurs/<?= $u['utilisateur_id'] ?>/suspendre" style="display:inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-danger" data-confirm="Suspendre ce compte ?" style="padding:0.4rem 0.75rem">
                                            <i class="bi bi-pause-fill"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="/admin/utilisateurs/<?= $u['utilisateur_id'] ?>/reactiver" style="display:inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-primary" style="padding:0.4rem 0.75rem">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="card" style="height:fit-content">
            <h3 style="margin-top:0">Créer un compte employé</h3>
            <form method="post" action="/admin/employes">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label">Pseudo</label>
                    <input type="text" name="pseudo" class="form-control" required minlength="3">
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <small style="color:var(--color-text-muted)">Min 8 car. avec maj/min/chiffre/spécial</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Créer l'employé</button>
            </form>
        </aside>
    </div>
</div>
