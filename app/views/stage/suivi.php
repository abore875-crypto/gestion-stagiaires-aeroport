<?php $titrePage = 'Suivi de ma demande';require __DIR__ . '/../partials/app-header.php'; ?>



<?php if (empty($demandes)): ?>
    <div class="alert alert-info">
        Vous n'avez pas encore déposé de demande de stage.
        <a href="/stage/depot" class="alert-link">Déposer une demande</a>
    </div>
<?php else: ?>
    <?php
        $badges = [
            'en_attente' => 'bg-warning-subtle text-warning-emphasis',
            'acceptee'   => 'bg-success-subtle text-success-emphasis',
            'refusee'    => 'bg-danger-subtle text-danger-emphasis',
        ];
        $libelles = [
            'en_attente' => 'En attente',
            'acceptee'   => 'Acceptée',
            'refusee'    => 'Refusée',
        ];
    ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-secondary small">
                    <th>Université</th>
                    <th>Filière</th>
                    <th>Date de dépôt</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td><?= htmlspecialchars($demande['universite']) ?></td>
                        <td><?= htmlspecialchars($demande['filiere']) ?></td>
                        <td class="text-secondary">
                            <?= (new DateTime($demande['date_demande']))->format('d/m/Y') ?>
                        </td>
                        <td>
                            <span class="badge <?= $badges[$demande['statut']] ?>">
                                <?= $libelles[$demande['statut']] ?>
                            </span>
                            <?php if ($demande['statut'] === 'refusee' && $demande['motif_refus']): ?>
                                <div class="small text-secondary mt-1"><?= htmlspecialchars($demande['motif_refus']) ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>