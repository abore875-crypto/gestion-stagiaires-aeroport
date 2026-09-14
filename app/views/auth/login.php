<?php $titrePage = 'Connexion'; require __DIR__ . '/../partials/header.php'; ?>

<div class="public-utility-page py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="icon-circle"><i class="bi bi-shield-lock"></i></div>
                    <h1 class="h4 fw-medium mb-4 text-center">Connexion</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $erreur): ?>
                                    <li><?= htmlspecialchars($erreur) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/connexion" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($old['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="mot_de_passe">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>

                    <p class="text-center text-secondary small mt-4 mb-0">
                        Pas encore de compte ? <a href="/inscription">Créer un compte stagiaire</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>