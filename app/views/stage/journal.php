<?php $titrePage = 'Mon journal de stage';require __DIR__ . '/../partials/app-header.php'; ?>

<?php if (!$stage): ?>
    <div class="alert alert-info">
        Vous n'avez pas encore de stage actif. Le journal sera disponible une fois
        votre demande acceptée. <a href="/stage/suivi" class="alert-link">Voir le statut de ma demande</a>
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

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 fw-medium mb-3">Ajouter une entrée</h2>
                    <form method="post" action="/stage/journal" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label small">Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_entree" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Activités réalisées</label>
                            <textarea class="form-control form-control-sm" name="activites" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Difficultés rencontrées (facultatif)</label>
                            <textarea class="form-control form-control-sm" name="difficultes" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Compétences acquises (facultatif)</label>
                            <textarea class="form-control form-control-sm" name="competences_acquises" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Pièce jointe PDF (facultatif)</label>
                            <input type="file" class="form-control form-control-sm" name="piece_jointe" accept="application/pdf">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">Ajouter l'entrée</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <h2 class="h6 fw-medium mb-3">Historique</h2>
            <?php if (empty($entrees)): ?>
                <div class="alert alert-info small">Aucune entrée pour le moment.</div>
            <?php else: ?>
                <?php foreach ($entrees as $entree): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-medium small"><?= (new DateTime($entree['date_entree']))->format('d/m/Y') ?></span>
                                <?php if ($entree['valide']): ?>
                                    <span class="badge bg-success-subtle text-success-emphasis">Validée</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis">En attente de validation</span>
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
                                <a href="/<?= htmlspecialchars($entree['piece_jointe']) ?>" target="_blank" class="small">Voir la pièce jointe</a>
                            <?php endif; ?>

                            <?php if ($entree['valide'] && $entree['commentaire_agent']): ?>
                                <div class="border-top mt-2 pt-2">
                                    <p class="small text-secondary mb-0">
                                        <strong>Commentaire de l'encadrant :</strong> <?= nl2br(htmlspecialchars($entree['commentaire_agent'])) ?>
                                        <?php if ($entree['note_evaluation'] !== null): ?>
                                            — Note : <?= htmlspecialchars($entree['note_evaluation']) ?>/20
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>