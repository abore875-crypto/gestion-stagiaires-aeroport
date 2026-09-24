<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica', Arial, sans-serif; color: #1a1a1a; padding: 40px; font-size: 12px; }
    .entete { display: table; width: 100%; margin-bottom: 30px; }
    .entete-gauche { display: table-cell; }
    .entete-droite { display: table-cell; text-align: right; }
    .entete h1 { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #555; margin: 0; }
    .entete p { font-size: 10px; color: #777; margin: 3px 0 0; }
    .ref { font-size: 10px; color: #777; }
    .titre { text-align: center; font-size: 18px; font-weight: bold; margin: 30px 0; text-transform: uppercase; text-decoration: underline; }
    .objet { margin-bottom: 20px; font-size: 12px; }
    .objet strong { text-decoration: underline; }
    .corps { line-height: 1.8; text-align: justify; margin: 0 10px 30px; }
    table.infos { width: 100%; border-collapse: collapse; margin: 20px 0; }
    table.infos td { border: 1px solid #ccc; padding: 8px 10px; font-size: 12px; }
    table.infos td.label { background: #f4f6f8; font-weight: bold; width: 35%; }
    .signature { margin-top: 60px; text-align: right; margin-right: 40px; }
    .signature p { margin: 2px 0; font-size: 12px; }
    .pied { position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; }
</style>
</head>
<body>

    <div class="entete">
        <div class="entete-gauche">
            <h1>Aéroport Stages</h1>
            <p>Direction des Ressources Humaines</p>
        </div>
        <div class="entete-droite">
            <span class="ref">N° NS-<?= str_pad((string) $stage['id'], 4, '0', STR_PAD_LEFT) ?>/DRH-<?= (new DateTime())->format('Y') ?></span>
        </div>
    </div>

    <div class="titre">Note de service</div>

    <div class="objet">
        <strong>Objet :</strong> Affectation d'un(e) stagiaire
    </div>

    <div class="corps">
        La Direction des Ressources Humaines porte à la connaissance du département
        <strong><?= htmlspecialchars($stage['departement_nom']) ?></strong> qu'un(e) stagiaire
        a été affecté(e) selon les modalités suivantes :
    </div>

    <table class="infos">
        <tr>
            <td class="label">Stagiaire</td>
            <td><?= htmlspecialchars($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']) ?></td>
        </tr>
        <tr>
            <td class="label">Département</td>
            <td><?= htmlspecialchars($stage['departement_nom']) ?></td>
        </tr>
        <tr>
            <td class="label">Section</td>
            <td><?= htmlspecialchars($stage['section_nom']) ?></td>
        </tr>
        <tr>
            <td class="label">Encadrant désigné</td>
            <td><?= htmlspecialchars($stage['agent_prenom'] . ' ' . $stage['agent_nom']) ?></td>
        </tr>
        <tr>
            <td class="label">Période de stage</td>
            <td>
                Du <?= (new DateTime($stage['date_debut']))->format('d/m/Y') ?>
                au <?= (new DateTime($stage['date_fin']))->format('d/m/Y') ?>
            </td>
        </tr>
    </table>

    <div class="corps">
        Le responsable de section est prié de bien vouloir accueillir le/la stagiaire
        susnommé(e) et de veiller au bon déroulement de son stage au sein du département.
    </div>

    <div class="signature">
        <p>Fait à Bamako, le <?= (new DateTime())->format('d/m/Y') ?></p>
        <p style="margin-top: 40px;">Le Directeur des Ressources Humaines</p>
    </div>

    <div class="pied">
        Document interne généré automatiquement — Aéroport Stages
    </div>

</body>
</html>