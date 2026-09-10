<?php $titrePage = 'Mon rapport de stage'; require __DIR__ . '/../partials/header.php'; ?>

<h1 class="h3 fw-medium mb-4">Mon rapport de stage</h1>

<?php if (!$stage): ?>
    <div class="alert alert-info">
        Vous n'avez pas encore de stage actif. Le dépôt du rapport sera disponible
        une fois votre demande acceptée.
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $erreur): ?>
                    <li><?= htmlspecialchars($erreur) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
        $badges = [
            'en_attente'   => 'bg-warning-subtle text-warning-emphasis',
            'valide_agent' => 'bg-info-subtle text-info-emphasis',
            'valide_rh'    => 'bg-success-subtle text-success-emphasis',
            'refuse'       => 'bg-danger-subtle text-danger-emphasis',
        ];
        $libelles = [
            'en_attente'   => 'En attente de validation par votre encadrant',
            'valide_agent' => 'Validé par l\'encadrant, en attente du RH',
            'valide_rh'    => 'Validé — votre attestation est en préparation',
            'refuse'       => 'Refusé — merci de redéposer une version corrigée',
        ];
    ?>

    <?php if ($rapport): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium">Rapport déposé</span>
                    <span class="badge <?= $badges[$rapport['statut']] ?>"><?= $libelles[$rapport['statut']] ?></span>
                </div>
                <p class="small text-secondary mb-2">
                    Déposé le <?= (new DateTime($rapport['date_depot']))->format('d/m/Y à H:i') ?>
                </p>
                <a href="/<?= htmlspecialchars($rapport['fichier_path']) ?>" target="_blank" class="small">Voir le fichier déposé</a>

                <?php if ($rapport['statut'] === 'refuse'): ?>
                    <?php if ($rapport['commentaire_agent']): ?>
                        <div class="alert alert-warning small mt-3 mb-0">
                            <strong>Commentaire de l'encadrant :</strong> <?= nl2br(htmlspecialchars($rapport['commentaire_agent'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($rapport['commentaire_rh']): ?>
                        <div class="alert alert-warning small mt-3 mb-0">
                            <strong>Commentaire du RH :</strong> <?= nl2br(htmlspecialchars($rapport['commentaire_rh'])) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$rapport || $rapport['statut'] === 'refuse'): ?>
        <div class="card">
            <div class="card-body">
                <h2 class="h6 fw-medium mb-3">
                    <?= $rapport ? 'Redéposer une version corrigée' : 'Déposer votre rapport final' ?>
                </h2>
                <form method="post" action="/stage/rapport" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                    <div class="mb-3">
                        <label class="form-label small">Fichier PDF (5 Mo max)</label>
                        <input type="file" class="form-control form-control-sm" name="rapport" accept="application/pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Déposer</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>