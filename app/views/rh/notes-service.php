<?php $titrePage = 'Notes de service'; require __DIR__ . '/../partials/app-header.php'; ?>

<?php if (empty($notes)): ?>
    <div class="alert alert-info">Aucune note de service générée pour le moment.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-secondary small">
                    <th>Stagiaire</th>
                    <th>Agent encadrant</th>
                    <th>Date de génération</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['stagiaire_prenom'] . ' ' . $note['stagiaire_nom']) ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($note['agent_prenom'] . ' ' . $note['agent_nom']) ?></td>
                        <td class="text-secondary small"><?= (new DateTime($note['date_generation']))->format('d/m/Y à H:i') ?></td>
                        <td>
                            <a href="/rh/notes-service/telecharger?stage_id=<?= $note['stage_id'] ?>" class="btn btn-sm btn-outline-primary">Télécharger</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>