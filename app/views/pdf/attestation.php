<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', Arial, sans-serif; color: #1a1a1a; padding: 40px; }
    .entete { text-align: center; margin-bottom: 40px; }
    .entete h1 { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #555; margin: 0; }
    .entete p { font-size: 11px; color: #777; margin: 4px 0 0; }
    .titre { text-align: center; font-size: 22px; font-weight: bold; margin: 40px 0; text-transform: uppercase; }
    .corps { font-size: 13px; line-height: 1.9; text-align: justify; margin: 0 20px; }
    .corps strong { font-weight: bold; }
    .signature { margin-top: 80px; text-align: right; margin-right: 40px; }
    .signature p { margin: 2px 0; font-size: 12px; }
    .pied { position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; }
</style>
</head>
<body>

    <div class="entete">
        <h1>Aéroport Stages</h1>
        <p>Direction des Ressources Humaines</p>
    </div>

    <div class="titre">Attestation de fin de stage</div>

    <div class="corps">
        <p>
            La Direction des Ressources Humaines de l'Aéroport atteste que
            <strong><?= htmlspecialchars($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']) ?></strong>
            a effectué un stage au sein du département
            <strong><?= htmlspecialchars($stage['departement_nom']) ?></strong>,
            section <strong><?= htmlspecialchars($stage['section_nom']) ?></strong>,
            du <strong><?= (new DateTime($stage['date_debut']))->format('d/m/Y') ?></strong>
            au <strong><?= (new DateTime($stage['date_fin']))->format('d/m/Y') ?></strong>.
        </p>
        <p>
            Ce stage a été encadré par
            <strong><?= htmlspecialchars($stage['agent_prenom'] . ' ' . $stage['agent_nom']) ?></strong>.
        </p>
        <p>
            Le rapport de fin de stage a été soumis, examiné, et validé par les
            parties concernées. Cette attestation est délivrée pour servir et valoir
            ce que de droit.
        </p>
    </div>

    <div class="signature">
        <p>Fait à Bamako, le <?= (new DateTime())->format('d/m/Y') ?></p>
        <p style="margin-top: 40px;">Le Directeur des Ressources Humaines</p>
    </div>

    <div class="pied">
        Document généré automatiquement — Aéroport Stages
    </div>

</body>
</html>