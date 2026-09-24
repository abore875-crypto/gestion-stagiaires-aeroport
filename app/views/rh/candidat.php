<?php $titrePage = 'Fiche candidat'; require __DIR__ . '/../partials/app-header.php'; ?>

<a href="/rh/tableau-de-bord" class="small text-secondary"><i class="bi bi-arrow-left"></i> Tableau de bord</a>

<h1 class="h4 fw-medium my-3"><?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?></h1>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 fw-medium mb-3">Demande</h2>
        <p class="small mb-1">Email : <?= htmlspecialchars($demande['email']) ?></p>
        <p class="small mb-1">Téléphone : <?= htmlspecialchars($demande['telephone']) ?></p>
        <p class="small mb-1">Université : <?= htmlspecialchars($demande['universite']) ?> — <?= htmlspecialchars($demande['filiere']) ?> (<?= htmlspecialchars($demande['niveau_etude']) ?>)</p>
        <p class="small mb-2">Statut : <strong><?= htmlspecialchars($demande['statut']) ?></strong></p>
        <a href="/<?= htmlspecialchars($demande['cv_path']) ?>" target="_blank" class="small">Voir le CV</a>
    </div>
</div>

<?php if ($stage): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 fw-medium mb-3">Stage</h2>
            <p class="small mb-1">Encadrant : <?= htmlspecialchars($stage['agent_prenom'] . ' ' . $stage['agent_nom']) ?></p>
            <p class="small mb-1">Section : <?= htmlspecialchars($stage['section_nom']) ?> (<?= htmlspecialchars($stage['departement_nom']) ?>)</p>
            <p class="small mb-0">Période : <?= (new DateTime($stage['date_debut']))->format('d/m/Y') ?> → <?= (new DateTime($stage['date_fin']))->format('d/m/Y') ?></p>
            <?php if (!$stage['stagiaire_id']): ?>
                <div class="alert alert-warning small mt-3 mb-0">
                    Le candidat n'a pas encore créé son compte stagiaire.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 fw-medium mb-3">Journal de bord (<?= count($entrees) ?>)</h2>
            <?php if (empty($entrees)): ?>
                <p class="small text-secondary mb-0">Aucune entrée pour le moment.</p>
            <?php else: ?>
                <?php foreach ($entrees as $entree): ?>
                    <div class="border-top pt-2 mt-2">
                        <p class="small fw-medium mb-1">
                            <?= (new DateTime($entree['date_entree']))->format('d/m/Y') ?>
                            — <?= $entree['valide'] ? '✅ Validée' : '🕓 En attente' ?>
                        </p>
                        <p class="small mb-0"><?= nl2br(htmlspecialchars($entree['activites'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 fw-medium mb-3">Rapport</h2>
            <?php if (!$rapport): ?>
                <p class="small text-secondary mb-0">Aucun rapport déposé pour le moment.</p>
            <?php else: ?>
                <p class="small mb-1">Statut : <?= htmlspecialchars($rapport['statut']) ?></p>
                <a href="/<?= htmlspecialchars($rapport['fichier_path']) ?>" target="_blank" class="small">Voir le fichier</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h6 fw-medium mb-3">Attestation</h2>
            <?php if (!$attestation): ?>
                <p class="small text-secondary mb-0">Pas encore générée.</p>
            <?php else: ?>
                <p class="small mb-2">Générée le <?= (new DateTime($attestation['date_generation']))->format('d/m/Y') ?></p>
                <a href="/rh/attestations/telecharger?stage_id=<?= $stage['id'] ?>" class="btn btn-sm btn-outline-primary">Télécharger</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>