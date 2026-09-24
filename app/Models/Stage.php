<?php
/**
 * app/Models/Stage.php
 * Toutes les requêtes SQL concernant la table `stages`.
 * stagiaire_id est nullable : un stage peut exister avant que le
 * candidat n'ait créé son compte (voir linkStagiaireByDemandeIds).
 */

class Stage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO stages
                (demande_id, stagiaire_id, departement_id, section_id, agent_id, date_debut, date_fin)
             VALUES
                (:demande_id, :stagiaire_id, :departement_id, :section_id, :agent_id, :date_debut, :date_fin)'
        );

        $stmt->execute([
            'demande_id'     => $data['demande_id'],
            'stagiaire_id'   => $data['stagiaire_id'] ?? null,
            'departement_id' => $data['departement_id'],
            'section_id'     => $data['section_id'],
            'agent_id'       => $data['agent_id'],
            'date_debut'     => $data['date_debut'],
            'date_fin'       => $data['date_fin'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByDemandeId(int $demandeId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM stages WHERE demande_id = :demande_id LIMIT 1');
        $stmt->execute(['demande_id' => $demandeId]);
        $s = $stmt->fetch();
        return $s ?: null;
    }

    public function findByAgentId(int $agentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT st.*, u.nom AS stagiaire_nom, u.prenom AS stagiaire_prenom, u.email AS stagiaire_email
             FROM stages st JOIN users u ON st.stagiaire_id = u.id
             WHERE st.agent_id = :agent_id ORDER BY st.date_debut DESC'
        );
        $stmt->execute(['agent_id' => $agentId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM stages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $s = $stmt->fetch();
        return $s ?: null;
    }

    public function findByStagiaireId(int $stagiaireId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM stages WHERE stagiaire_id = :stagiaire_id ORDER BY date_debut DESC LIMIT 1'
        );
        $stmt->execute(['stagiaire_id' => $stagiaireId]);
        $s = $stmt->fetch();
        return $s ?: null;
    }

    /**
     * Renvoie un stage avec toutes les infos détaillées. LEFT JOIN sur
     * le stagiaire car son compte peut ne pas encore exister.
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT st.*,
                    stag.nom AS stagiaire_nom, stag.prenom AS stagiaire_prenom,
                    ag.nom AS agent_nom, ag.prenom AS agent_prenom,
                    sec.nom AS section_nom, dep.nom AS departement_nom
             FROM stages st
             LEFT JOIN users stag ON st.stagiaire_id = stag.id
             JOIN users ag ON st.agent_id = ag.id
             JOIN sections sec ON st.section_id = sec.id
             JOIN departements dep ON st.departement_id = dep.id
             WHERE st.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $s = $stmt->fetch();
        return $s ?: null;
    }

    /**
     * Relie le(s) stage(s) liés aux demandes données à un compte
     * stagiaire fraîchement créé (via leur demande_id).
     */
    public function linkStagiaireByDemandeIds(array $demandeIds, int $stagiaireId): void
    {
        if (empty($demandeIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($demandeIds), '?'));
        $stmt = $this->db->prepare(
            "UPDATE stages SET stagiaire_id = ? WHERE demande_id IN ($placeholders)"
        );
        $stmt->execute(array_merge([$stagiaireId], $demandeIds));
    }
}