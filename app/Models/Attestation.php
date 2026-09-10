<?php
/**
 * app/Models/Attestation.php
 * Toutes les requêtes SQL concernant la table `attestations`.
 * Une attestation est générée automatiquement quand le RH valide
 * définitivement le rapport d'un stage (relation 1-1 avec `stages`).
 */

class Attestation
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $stageId, string $fichierPath): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO attestations (stage_id, fichier_path) VALUES (:stage_id, :fichier_path)'
        );
        $stmt->execute(['stage_id' => $stageId, 'fichier_path' => $fichierPath]);

        return (int) $this->db->lastInsertId();
    }

    public function findByStageId(int $stageId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM attestations WHERE stage_id = :stage_id LIMIT 1');
        $stmt->execute(['stage_id' => $stageId]);
        $attestation = $stmt->fetch();

        return $attestation ?: null;
    }

    public function findAllWithStagiaire(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, u.nom AS stagiaire_nom, u.prenom AS stagiaire_prenom
             FROM attestations a
             JOIN stages st ON a.stage_id = st.id
             JOIN users u ON st.stagiaire_id = u.id
             ORDER BY a.date_generation DESC'
        );

        return $stmt->fetchAll();
    }
}