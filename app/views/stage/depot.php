<?php $titrePage = 'Déposer une demande'; require __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <h1 class="h3 fw-medium mb-4 text-center">Déposer une demande de stage</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/stage/depot" enctype="multipart/form-data" novalidate>
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="telephone">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone"
                           value="<?= htmlspecialchars($old['telephone']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($old['email']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="universite">Université / École</label>
                <input type="text" class="form-control" id="universite" name="universite"
                       value="<?= htmlspecialchars($old['universite']) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="filiere">Filière</label>
                    <input type="text" class="form-control" id="filiere" name="filiere"
                           value="<?= htmlspecialchars($old['filiere']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="niveau_etude">Niveau d'étude</label>
                    <input type="text" class="form-control" id="niveau_etude" name="niveau_etude"
                           value="<?= htmlspecialchars($old['niveau_etude']) ?>"
                           placeholder="ex : Licence 3" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="cv">CV (PDF, 5 Mo max)</label>
                <input type="file" class="form-control" id="cv" name="cv" accept="application/pdf" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="lettre_motivation">Lettre de motivation (PDF, facultatif)</label>
                <input type="file" class="form-control" id="lettre_motivation" name="lettre_motivation" accept="application/pdf">
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Envoyer ma demande</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>