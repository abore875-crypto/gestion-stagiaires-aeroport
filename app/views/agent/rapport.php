<?php $titrePage = 'Rapport de ' . $stagiaire['prenom'];require __DIR__ . '/../partials/app-header.php'; ?>

<a href="/agent/tableau-de-bord" class="small">← Mes stagiaires</a>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $erreur): ?>
                <li><?= htmlspecialchars($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!$rapport): ?>
    <div class="alert alert-info">Ce stagiaire n'a pas encore déposé de rapport.</div>
<?php else: ?>
    <?php
        $badges = [
            'en_attente'   => 'bg-warning-subtle text-warning-emphasis',
            'valide_agent' => 'bg-info-subtle text-info-emphasis',
            'valide_rh'    => 'bg-success-subtle text-success-emphasis',
            'refuse'       => 'bg-danger-subtle text-danger-emphasis',
        ];
        $libelles = [
            'en_attente'   => 'En attente de votre validation',
            'valide_agent' => 'Validé par vous, en attente du RH',
            'valide_rh'    => 'Validé définitivement',
            'refuse'       => 'Refusé',
        ];
    ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-medium">Rapport déposé</span>
                <span class="badge <?= $badges[$rapport['statut']] ?>"><?= $libelles[$rapport['statut']] ?></span>
            </div>
            <p class="small text-secondary mb-2">
                Déposé le <?= (new DateTime($rapport['date_depot']))->format('d/m/Y à H:i') ?>
            </p>
            <a href="/<?= htmlspecialchars($rapport['fichier_path']) ?>" target="_blank" class="small d-block mb-3">Voir le fichier</a>

            <?php if ($rapport['statut'] === 'en_attente'): ?>
                <form method="post" action="/agent/rapport?stage_id=<?= $stage['id'] ?>" class="border-top pt-3">
                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                    <input type="hidden" name="stage_id" value="<?= $stage['id'] ?>">
                    <input type="hidden" name="rapport_id" value="<?= $rapport['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label small">Commentaire</label>
                        <textarea name="commentaire" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" name="action" value="valider" class="btn btn-sm btn-success">Valider et transmettre au RH</button>
                    <button type="submit" name="action" value="refuser" class="btn btn-sm btn-outline-danger">Refuser</button>
                </form>
            <?php elseif ($rapport['commentaire_agent']): ?>
                <p class="small text-secondary mb-0"><strong>Votre commentaire :</strong> <?= nl2br(htmlspecialchars($rapport['commentaire_agent'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>