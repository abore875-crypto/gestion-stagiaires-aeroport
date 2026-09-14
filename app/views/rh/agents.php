<?php $titrePage = 'Gestion des agents'; require __DIR__ . '/../partials/app-header.php'; ?>
<div class="d-flex align-items-center justify-content-between mb-4">

    <a href="/rh/tableau-de-bord" class="btn btn-outline-secondary btn-sm">← Tableau de bord</a>
</div>

<div class="row g-4">
    <!-- Formulaire de création -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 fw-medium mb-3">Créer un compte agent</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3 small">
                            <?php foreach ($errors as $erreur): ?>
                                <li><?= htmlspecialchars($erreur) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="/rh/agents" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label small">Nom</label>
                        <input type="text" class="form-control form-control-sm" name="nom"
                               value="<?= htmlspecialchars($old['nom']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Prénom</label>
                        <input type="text" class="form-control form-control-sm" name="prenom"
                               value="<?= htmlspecialchars($old['prenom']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Email</label>
                        <input type="email" class="form-control form-control-sm" name="email"
                               value="<?= htmlspecialchars($old['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Téléphone</label>
                        <input type="tel" class="form-control form-control-sm" name="telephone"
                               value="<?= htmlspecialchars($old['telephone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Section encadrée</label>
                        <select class="form-select form-select-sm" name="section_id" required>
                            <option value="">— Choisir une section —</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?= $section['id'] ?>"
                                    <?= (string)($old['section_id'] ?? '') === (string)$section['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($section['section_nom']) ?> (<?= htmlspecialchars($section['departement_nom']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Mot de passe temporaire</label>
                        <input type="password" class="form-control form-control-sm" name="mot_de_passe"
                               minlength="8" required>
                        <div class="form-text">À communiquer à l'agent, 8 caractères minimum.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">Créer l'agent</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des agents existants -->
    <div class="col-lg-8">
        <?php if (empty($agents)): ?>
            <div class="alert alert-info">Aucun agent créé pour le moment.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small">
                            <th>Agent</th>
                            <th>Section</th>
                            <th>Contact</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agents as $agent): ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?></td>
                                <td>
                                    <?php if ($agent['section_nom']): ?>
                                        <?= htmlspecialchars($agent['section_nom']) ?>
                                        <div class="small text-secondary"><?= htmlspecialchars($agent['departement_nom']) ?></div>
                                    <?php else: ?>
                                        <span class="text-secondary small">Aucune section</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-secondary">
                                    <?= htmlspecialchars($agent['email']) ?><br>
                                    <?= htmlspecialchars($agent['telephone'] ?? '—') ?>
                                </td>
                                <td>
                                    <?php if ($agent['actif']): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Désactivé</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>