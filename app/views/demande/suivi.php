<?php $titrePage = 'Suivre ma demande'; require __DIR__ . '/../partials/header.php'; ?>

<div class="public-utility-page py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="icon-circle"><i class="bi bi-search"></i></div>
                    <h1 class="h4 fw-medium mb-4 text-center">Suivre ma demande</h1>

                    <?php if ($vientDeDeposer): ?>
                        <div class="alert alert-success">
                            Votre demande a bien été envoyée ! Entrez ci-dessous votre email et
                            votre téléphone à tout moment pour suivre son traitement.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $erreur): ?>
                                    <li><?= htmlspecialchars($erreur) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/demande/suivi" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="telephone" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Voir le statut</button>
                    </form>
                </div>
            </div>

            <?php if ($demandes !== null && !empty($demandes)): ?>
                <?php
                    $badges = [
                        'en_attente' => 'bg-warning-subtle text-warning-emphasis',
                        'acceptee'   => 'bg-success-subtle text-success-emphasis',
                        'refusee'    => 'bg-danger-subtle text-danger-emphasis',
                    ];
                    $libelles = ['en_attente' => 'En attente', 'acceptee' => 'Acceptée', 'refusee' => 'Refusée'];
                ?>
                <?php foreach ($demandes as $demande): ?>
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="fw-medium mb-0"><?= htmlspecialchars($demande['universite']) ?></p>
                                    <p class="small text-secondary mb-0"><?= htmlspecialchars($demande['filiere']) ?> — <?= htmlspecialchars($demande['niveau_etude']) ?></p>
                                </div>
                                <span class="badge <?= $badges[$demande['statut']] ?>"><?= $libelles[$demande['statut']] ?></span>
                            </div>
                            <p class="small text-secondary mb-2">
                                Déposée le <?= (new DateTime($demande['date_demande']))->format('d/m/Y') ?>
                            </p>

                            <?php if ($demande['statut'] === 'refusee' && $demande['motif_refus']): ?>
                                <p class="small text-secondary mb-0"><strong>Motif :</strong> <?= htmlspecialchars($demande['motif_refus']) ?></p>
                            <?php endif; ?>

                            <?php if ($demande['statut'] === 'acceptee'): ?>
                                <div class="alert alert-success small mt-3 mb-0">
                                    🎉 Votre demande a été acceptée ! Vous pouvez maintenant
                                    <a href="/inscription">créer votre compte stagiaire</a>
                                    avec ce même email pour accéder à votre note de service,
                                    votre journal et le reste de votre espace stagiaire.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>