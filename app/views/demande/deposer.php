<?php $titrePage = 'Déposer une demande'; require __DIR__ . '/../partials/header.php'; ?>

<div class="public-utility-page py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="icon-circle"><i class="bi bi-file-earmark-plus"></i></div>
                    <h1 class="h4 fw-medium mb-4 text-center">Déposer une demande de stage</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $erreur): ?>
                                    <li><?= htmlspecialchars($erreur) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/demande/deposer" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="nom"
                                       value="<?= htmlspecialchars($old['nom']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="prenom"
                                       value="<?= htmlspecialchars($old['prenom']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" name="telephone"
                                       value="<?= htmlspecialchars($old['telephone']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email"
                                       value="<?= htmlspecialchars($old['email']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Université / École</label>
                            <input type="text" class="form-control" name="universite"
                                   value="<?= htmlspecialchars($old['universite']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Filière</label>
                                <input type="text" class="form-control" name="filiere"
                                       value="<?= htmlspecialchars($old['filiere']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Niveau d'étude</label>
                                <input type="text" class="form-control" name="niveau_etude"
                                       value="<?= htmlspecialchars($old['niveau_etude']) ?>"
                                       placeholder="ex : Licence 3" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CV (PDF, 5 Mo max)</label>
                            <input type="file" class="form-control" name="cv" accept="application/pdf" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lettre de motivation (PDF, facultatif)</label>
                            <input type="file" class="form-control" name="lettre_motivation" accept="application/pdf">
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle"></i>
                            Aucun compte n'est nécessaire pour déposer votre demande. Notez bien
                            l'email et le téléphone renseignés ci-dessus : ils vous serviront à
                            suivre l'avancement de votre dossier.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Envoyer ma demande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>