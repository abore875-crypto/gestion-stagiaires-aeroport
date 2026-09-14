<?php $titrePage = 'Attestations générées'; require __DIR__ . '/../partials/app-header.php'; ?>


    <a href="/rh/tableau-de-bord" class="btn btn-outline-secondary btn-sm">← Tableau de bord</a>
</div>

<?php if (empty($attestations)): ?>
    <div class="alert alert-info">Aucune attestation générée pour le moment.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-secondary small">
                    <th>Stagiaire</th>
                    <th>Date de génération</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attestations as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['stagiaire_prenom'] . ' ' . $a['stagiaire_nom']) ?></td>
                        <td class="text-secondary small"><?= (new DateTime($a['date_generation']))->format('d/m/Y à H:i') ?></td>
                        <td>
                            <a href="/rh/attestations/telecharger?stage_id=<?= $a['stage_id'] ?>" class="btn btn-sm btn-outline-primary">Télécharger</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>