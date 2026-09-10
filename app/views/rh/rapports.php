<?php $titrePage = 'Rapports à valider'; require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 fw-medium mb-0">Rapports à valider</h1>
    <a href="/rh/tableau-de-bord" class="btn btn-outline-secondary btn-sm">← Tableau de bord</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $erreur): ?>
                <li><?= htmlspecialchars($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($rapports)): ?>
    <div class="alert alert-info">
        Aucun rapport en attente de validation finale pour le moment.
    </div>
<?php else: ?>
    <?php foreach ($rapports as $rapport): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium"><?= htmlspecialchars($rapport['stagiaire_prenom'] . ' ' . $rapport['stagiaire_nom']) ?></span>
                    <span class="badge bg-info-subtle text-info-emphasis">Validé par l'encadrant</span>
                </div>
                <p class="small text-secondary mb-2">
                    Validé le <?= (new DateTime($rapport['date_validation_agent']))->format('d/m/Y') ?>
                </p>
                <a href="/<?= htmlspecialchars($rapport['fichier_path']) ?>" target="_blank" class="small d-block mb-2">Voir le fichier</a>

                <?php if ($rapport['commentaire_agent']): ?>
                    <p class="small text-secondary mb-3"><strong>Commentaire de l'encadrant :</strong> <?= nl2br(htmlspecialchars($rapport['commentaire_agent'])) ?></p>
                <?php endif; ?>

                <form method="post" action="/rh/rapports" class="border-top pt-3">
                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                    <input type="hidden" name="rapport_id" value="<?= $rapport['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label small">Commentaire (facultatif)</label>
                        <textarea name="commentaire" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" name="action" value="valider" class="btn btn-sm btn-success">Valider définitivement</button>
                    <button type="submit" name="action" value="refuser" class="btn btn-sm btn-outline-danger">Refuser</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>