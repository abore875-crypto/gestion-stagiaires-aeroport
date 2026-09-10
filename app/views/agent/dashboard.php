<?php $titrePage = 'Mes stagiaires'; require __DIR__ . '/../partials/header.php'; ?>
<div class="page-header">
    <div class="icon-box">👥</div>
    <div>
        <h1 class="h4 fw-medium mb-0">Mes stagiaires</h1>
        <p class="text-secondary small mb-0">Suivi et encadrement de vos stagiaires affectés</p>
    </div>
</div>


<?php if (empty($stages)): ?>
    <div class="alert alert-info">
        Aucun stagiaire ne vous a été affecté pour le moment.
    </div>
<?php else: ?>
    <?php
        $badges = [
            'a_venir' => 'bg-secondary-subtle text-secondary-emphasis',
            'en_cours' => 'bg-success-subtle text-success-emphasis',
            'termine' => 'bg-primary-subtle text-primary-emphasis',
        ];
        $libelles = [
            'a_venir' => 'À venir',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
        ];
    ?>
    <div class="row g-3">
        <?php foreach ($stages as $stage): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-medium"><?= htmlspecialchars($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']) ?></span>
                            <span class="badge <?= $badges[$stage['statut']] ?>"><?= $libelles[$stage['statut']] ?></span>
                        </div>
                        <p class="small text-secondary mb-2"><?= htmlspecialchars($stage['stagiaire_email']) ?></p>
                                               <p class="small mb-2">
                            <?= (new DateTime($stage['date_debut']))->format('d/m/Y') ?>
                            →
                            <?= (new DateTime($stage['date_fin']))->format('d/m/Y') ?>
                        </p>
                        <a href="/agent/journal?stage_id=<?= $stage['id'] ?>" class="btn btn-sm btn-outline-primary w-100">Voir le journal</a>
                        <a href="/agent/rapport?stage_id=<?= $stage['id'] ?>" class="btn btn-sm btn-outline-secondary w-100 mt-1">Voir le rapport</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>