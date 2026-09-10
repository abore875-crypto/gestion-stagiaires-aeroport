<?php
/**
 * app/Models/Rapport.php
 * Toutes les requêtes SQL concernant la table `rapports`.
 * Un stage n'a qu'un seul rapport (relation 1-1) : s'il est refusé,
 * le stagiaire re-dépose un fichier qui REMPLACE l'ancien (pas de doublon).
 */

class Rapport
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Renvoie le rapport d'un stage donné, ou null s'il n'existe pas encore.
     */
    public function findByStageId(int $stageId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM rapports WHERE stage_id = :stage_id LIMIT 1');
        $stmt->execute(['stage_id' => $stageId]);
        $rapport = $stmt->fetch();

        return $rapport ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM rapports WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $rapport = $stmt->fetch();

        return $rapport ?: null;
    }

    /**
     * Dépose ou remplace le rapport d'un stage : si aucun rapport n'existe
     * encore pour ce stage, on l'insère ; s'il en existe déjà un (ex: après
     * un refus), on remplace le fichier et on repart sur le statut 'en_attente'.
     */
    public function deposer(int $stageId, string $fichierPath, string $typeFichier): void
    {
        $existant = $this->findByStageId($stageId);

        if ($existant) {
            $stmt = $this->db->prepare(
                'UPDATE rapports
                 SET fichier_path = :fichier_path, type_fichier = :type_fichier,
                     date_depot = NOW(), statut = \'en_attente\',
                     commentaire_agent = NULL, commentaire_rh = NULL,
                     date_validation_agent = NULL, date_validation_rh = NULL
                 WHERE stage_id = :stage_id'
            );
            $stmt->execute([
                'fichier_path' => $fichierPath,
                'type_fichier' => $typeFichier,
                'stage_id'     => $stageId,
            ]);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO rapports (stage_id, fichier_path, type_fichier)
                 VALUES (:stage_id, :fichier_path, :type_fichier)'
            );
            $stmt->execute([
                'stage_id'     => $stageId,
                'fichier_path' => $fichierPath,
                'type_fichier' => $typeFichier,
            ]);
        }
    }

    /**
     * Validation par l'agent encadrant : passe le rapport en 'valide_agent',
     * prêt pour la validation finale du RH.
     */
    public function validerParAgent(int $id, ?string $commentaire): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE rapports
             SET statut = \'valide_agent\', commentaire_agent = :commentaire, date_validation_agent = NOW()
             WHERE id = :id'
        );

        return $stmt->execute(['commentaire' => $commentaire, 'id' => $id]);
    }

    /**
     * Validation finale par le RH : passe le rapport en 'valide_rh'.
     * C'est ce statut qui déclenchera la génération de l'attestation.
     */
    public function validerParRh(int $id, ?string $commentaire): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE rapports
             SET statut = \'valide_rh\', commentaire_rh = :commentaire, date_validation_rh = NOW()
             WHERE id = :id'
        );

        return $stmt->execute(['commentaire' => $commentaire, 'id' => $id]);
    }

    /**
     * Refus (par l'agent ou le RH) : le stagiaire devra re-déposer un fichier.
     */
    public function refuser(int $id, string $par, ?string $commentaire): bool
    {
        $champCommentaire = $par === 'agent' ? 'commentaire_agent' : 'commentaire_rh';

        $stmt = $this->db->prepare(
            "UPDATE rapports SET statut = 'refuse', {$champCommentaire} = :commentaire WHERE id = :id"
        );

        return $stmt->execute(['commentaire' => $commentaire, 'id' => $id]);
    }

    /**
     * Liste les rapports en attente de validation RH (statut 'valide_agent'),
     * avec les infos du stagiaire — pour le tableau de bord des rapports du RH.
     */
    public function findEnAttenteRh(): array
    {
        $stmt = $this->db->query(
            "SELECT r.*, u.nom AS stagiaire_nom, u.prenom AS stagiaire_prenom
             FROM rapports r
             JOIN stages st ON r.stage_id = st.id
             JOIN users u ON st.stagiaire_id = u.id
             WHERE r.statut = 'valide_agent'
             ORDER BY r.date_validation_agent ASC"
        );

        return $stmt->fetchAll();
    }
}