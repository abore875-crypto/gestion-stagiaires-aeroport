<?php
/**
 * app/Models/JournalStage.php
 * Toutes les requêtes SQL concernant la table `journaux_stage`.
 * Une entrée de journal est écrite par le stagiaire, puis validée
 * (avec commentaire et note facultative) par l'agent encadrant.
 */

class JournalStage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Crée une nouvelle entrée de journal et renvoie son id.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO journaux_stage
                (stage_id, date_entree, activites, difficultes, competences_acquises, piece_jointe)
             VALUES
                (:stage_id, :date_entree, :activites, :difficultes, :competences_acquises, :piece_jointe)'
        );

        $stmt->execute([
            'stage_id'             => $data['stage_id'],
            'date_entree'          => $data['date_entree'],
            'activites'            => $data['activites'],
            'difficultes'          => $data['difficultes'] ?? null,
            'competences_acquises' => $data['competences_acquises'] ?? null,
            'piece_jointe'         => $data['piece_jointe'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Renvoie toutes les entrées de journal d'un stage donné,
     * de la plus récente à la plus ancienne.
     */
    public function findByStageId(int $stageId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM journaux_stage
             WHERE stage_id = :stage_id
             ORDER BY date_entree DESC'
        );
        $stmt->execute(['stage_id' => $stageId]);

        return $stmt->fetchAll();
    }

    /**
     * Renvoie une entrée précise par son id.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM journaux_stage WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $entree = $stmt->fetch();

        return $entree ?: null;
    }

    /**
     * Valide une entrée de journal : l'agent ajoute un commentaire
     * et une note facultative, et marque l'entrée comme validée.
     */
    public function valider(int $id, ?string $commentaire, ?float $note): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE journaux_stage
             SET commentaire_agent = :commentaire, note_evaluation = :note, valide = 1
             WHERE id = :id'
        );

        return $stmt->execute([
            'commentaire' => $commentaire,
            'note'        => $note,
            'id'          => $id,
        ]);
    }
}