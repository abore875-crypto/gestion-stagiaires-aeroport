<?php $titrePage = 'Tableau de bord RH'; require __DIR__ . '/../partials/app-header.php'; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $erreur): ?>
                <li><?= htmlspecialchars($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-jaune">
            <div class="value"><?= $stats['en_attente'] ?></div>
            <div class="label">En attente</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-vert">
            <div class="value"><?= $stats['acceptee'] ?></div>
            <div class="label">Acceptées</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-rouge">
            <div class="value"><?= $stats['refusee'] ?></div>
            <div class="label">Refusées</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-gris">
            <div class="value"><?= count($agents) ?></div>
            <div class="label">Agents actifs</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group" role="group">
        <a href="/rh/tableau-de-bord" class="btn btn-sm <?= $statutFiltre === null ? 'btn-primary' : 'btn-outline-secondary' ?>">Toutes</a>
        <a href="/rh/tableau-de-bord?statut=en_attente" class="btn btn-sm <?= $statutFiltre === 'en_attente' ? 'btn-primary' : 'btn-outline-secondary' ?>">En attente</a>
        <a href="/rh/tableau-de-bord?statut=acceptee" class="btn btn-sm <?= $statutFiltre === 'acceptee' ? 'btn-primary' : 'btn-outline-secondary' ?>">Acceptées</a>
        <a href="/rh/tableau-de-bord?statut=refusee" class="btn btn-sm <?= $statutFiltre === 'refusee' ? 'btn-primary' : 'btn-outline-secondary' ?>">Refusées</a>
    </div>
    <a href="/rh/agents" class="btn btn-outline-secondary btn-sm">Gérer les agents</a>
</div>

<?php if (empty($demandes)): ?>
    <div class="alert alert-info">Aucune demande à afficher pour ce filtre.</div>
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
                    <th>Candidat</th>
                    <th>Université / Filière</th>
                    <th>Date</th>
                    <th>CV</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td>
                            <a href="/rh/candidat?demande_id=<?= $demande['id'] ?>" class="fw-medium text-decoration-none"><?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?></a>
                            <div class="small text-secondary"><?= htmlspecialchars($demande['email']) ?></div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($demande['universite']) ?></div>
                            <div class="small text-secondary"><?= htmlspecialchars($demande['filiere']) ?> — <?= htmlspecialchars($demande['niveau_etude']) ?></div>
                        </td>
                        <td class="text-secondary small">
                            <?= (new DateTime($demande['date_demande']))->format('d/m/Y') ?>
                        </td>
                        <td>
                            <a href="/<?= htmlspecialchars($demande['cv_path']) ?>" target="_blank" class="small">Voir le CV</a>
                        </td>
                        <td>
                            <span class="badge <?= $badges[$demande['statut']] ?>"><?= $libelles[$demande['statut']] ?></span>
                            <?php if ($demande['statut'] === 'refusee' && $demande['motif_refus']): ?>
                                <div class="small text-secondary mt-1"><?= htmlspecialchars($demande['motif_refus']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($demande['statut'] === 'en_attente'): ?>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#accepterModal<?= $demande['id'] ?>">
                                    Accepter
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#refuserModal<?= $demande['id'] ?>">
                                    Refuser
                                </button>

                                <div class="modal fade" id="accepterModal<?= $demande['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" action="/rh/tableau-de-bord">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Accepter la demande</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                                                    <input type="hidden" name="demande_id" value="<?= $demande['id'] ?>">
                                                    <input type="hidden" name="action" value="accepter">

                                                    <?php if (empty($agents)): ?>
                                                        <div class="alert alert-warning small mb-0">
                                                            Aucun agent disponible. <a href="/rh/agents">Crée d'abord un compte agent</a>.
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="mb-3">
                                                            <label class="form-label small">Agent encadrant</label>
                                                            <select class="form-select form-select-sm" name="agent_id" required>
                                                                <option value="">— Choisir un agent —</option>
                                                                <?php foreach ($agents as $agent): ?>
                                                                    <option value="<?= $agent['id'] ?>">
                                                                        <?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>
                                                                        <?php if ($agent['section_nom']): ?>
                                                                            — <?= htmlspecialchars($agent['section_nom']) ?>
                                                                        <?php endif; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 mb-3">
                                                                <label class="form-label small">Date de début</label>
                                                                <input type="date" class="form-control form-control-sm" name="date_debut" required>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <label class="form-label small">Date de fin</label>
                                                                <input type="date" class="form-control form-control-sm" name="date_fin" required>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <?php if (!empty($agents)): ?>
                                                        <button type="submit" class="btn btn-success">Confirmer l'acceptation</button>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="refuserModal<?= $demande['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" action="/rh/tableau-de-bord">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Refuser la demande</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                                                    <input type="hidden" name="demande_id" value="<?= $demande['id'] ?>">
                                                    <input type="hidden" name="action" value="refuser">
                                                    <label class="form-label">Motif du refus (optionnel)</label>
                                                    <textarea name="motif_refus" class="form-control" rows="3"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">Confirmer le refus</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-secondary small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/app-footer.php'; ?>