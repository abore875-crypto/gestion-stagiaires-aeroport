<?php $titrePage = 'Créer un compte'; require __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h3 fw-medium mb-2 text-center">Créer un compte stagiaire</h1>
        <p class="text-center text-secondary small mb-4">
            Ce formulaire ne concerne que les stagiaires. Les comptes RH et
            encadrant sont créés depuis l'espace RH.
        </p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/inscription" novalidate>
            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom"
                           value="<?= htmlspecialchars($old['nom']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom"
                           value="<?= htmlspecialchars($old['prenom']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($old['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="telephone">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone"
                       value="<?= htmlspecialchars($old['telephone']) ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="mot_de_passe">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe"
                           minlength="8" required>
                    <div class="form-text">8 caractères minimum.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="confirmation">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirmation" name="confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Créer mon compte</button>
        </form>

        <p class="text-center text-secondary small mt-4">
            Déjà un compte ? <a href="/connexion">Se connecter</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>