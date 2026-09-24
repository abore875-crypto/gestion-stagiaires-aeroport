<?php
/**
 * app/Models/NoteService.php
 * Toutes les requêtes SQL concernant la table `notes_service`.
 * Une note de service est générée automatiquement dès que le RH
 * accepte une demande et crée le stage correspondant.
 */

class NoteService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $stageId, string $fichierPath): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notes_service (stage_id, fichier_path) VALUES (:stage_id, :fichier_path)'
        );
        $stmt->execute(['stage_id' => $stageId, 'fichier_path' => $fichierPath]);

        return (int) $this->db->lastInsertId();
    }

    public function findByStageId(int $stageId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM notes_service WHERE stage_id = :stage_id LIMIT 1');
        $stmt->execute(['stage_id' => $stageId]);
        $note = $stmt->fetch();

        return $note ?: null;
    }

    /**
     * Liste toutes les notes de service générées, avec le nom du
     * stagiaire et de l'agent concerné — pour la page RH.
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->db->query(
            'SELECT n.*, u.nom AS stagiaire_nom, u.prenom AS stagiaire_prenom,
                    ag.nom AS agent_nom, ag.prenom AS agent_prenom
             FROM notes_service n
             JOIN stages st ON n.stage_id = st.id
             JOIN users u ON st.stagiaire_id = u.id
             JOIN users ag ON st.agent_id = ag.id
             ORDER BY n.date_generation DESC'
        );

        return $stmt->fetchAll();
    }
}