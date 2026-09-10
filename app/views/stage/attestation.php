<?php $titrePage = 'Mon attestation'; require __DIR__ . '/../partials/header.php'; ?>

<h1 class="h3 fw-medium mb-4">Mon attestation de fin de stage</h1>

<?php if (!$stage): ?>
    <div class="alert alert-info">Vous n'avez pas encore de stage actif.</div>
<?php elseif (!$attestation): ?>
    <div class="alert alert-info">
        Votre attestation n'a pas encore été générée. Elle sera disponible
        automatiquement dès que votre rapport final aura été validé par le RH.
        <a href="/stage/rapport" class="alert-link">Voir le statut de mon rapport</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <p class="fw-medium mb-2">🎉 Votre attestation est prête !</p>
            <p class="small text-secondary mb-3">
                Générée le <?= (new DateTime($attestation['date_generation']))->format('d/m/Y à H:i') ?>
            </p>
            <a href="/stage/attestation/telecharger" class="btn btn-primary btn-sm">Télécharger mon attestation (PDF)</a>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>