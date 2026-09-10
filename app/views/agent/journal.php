<?php $titrePage = 'Journal de ' . $stagiaire['prenom']; require __DIR__ . '/../partials/header.php'; ?>

<a href="/agent/tableau-de-bord" class="small">← Mes stagiaires</a>
<h1 class="h3 fw-medium my-3">
    Journal de <?= htmlspecialchars($stagiaire['prenom'] . ' ' . $stagiaire['nom']) ?>
</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $erreur): ?>
                <li><?= htmlspecialchars($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($entrees)): ?>
    <div class="alert alert-info">Ce stagiaire n'a pas encore ajouté d'entrée à son journal.</div>
<?php else: ?>
    <?php foreach ($entrees as $entree): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-medium small"><?= (new DateTime($entree['date_entree']))->format('d/m/Y') ?></span>
                    <?php if ($entree['valide']): ?>
                        <span class="badge bg-success-subtle text-success-emphasis">Validée</span>
                    <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis">À valider</span>
                    <?php endif; ?>
                </div>

                <p class="small mb-1"><strong>Activités :</strong> <?= nl2br(htmlspecialchars($entree['activites'])) ?></p>
                <?php if ($entree['difficultes']): ?>
                    <p class="small text-secondary mb-1"><strong>Difficultés :</strong> <?= nl2br(htmlspecialchars($entree['difficultes'])) ?></p>
                <?php endif; ?>
                <?php if ($entree['competences_acquises']): ?>
                    <p class="small text-secondary mb-1"><strong>Compétences :</strong> <?= nl2br(htmlspecialchars($entree['competences_acquises'])) ?></p>
                <?php endif; ?>
                <?php if ($entree['piece_jointe']): ?>
                    <a href="/<?= htmlspecialchars($entree['piece_jointe']) ?>" target="_blank" class="small d-block mb-2">Voir la pièce jointe</a>
                <?php endif; ?>

                <?php if ($entree['valide']): ?>
                    <div class="border-top mt-2 pt-2">
                        <p class="small text-secondary mb-0">
                            <strong>Votre commentaire :</strong> <?= nl2br(htmlspecialchars($entree['commentaire_agent'] ?? '')) ?>
                            <?php if ($entree['note_evaluation'] !== null): ?>
                                — Note : <?= htmlspecialchars($entree['note_evaluation']) ?>/20
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <form method="post" action="/agent/journal?stage_id=<?= $stage['id'] ?>" class="border-top mt-2 pt-3">
                        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                        <input type="hidden" name="stage_id" value="<?= $stage['id'] ?>">
                        <input type="hidden" name="journal_id" value="<?= $entree['id'] ?>">

                        <div class="mb-2">
                            <label class="form-label small">Commentaire (facultatif)</label>
                            <textarea name="commentaire_agent" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label class="form-label small">Note /20 (facultatif)</label>
                            <input type="number" name="note_evaluation" class="form-control form-control-sm" min="0" max="20" step="0.5">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Valider cette entrée</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>