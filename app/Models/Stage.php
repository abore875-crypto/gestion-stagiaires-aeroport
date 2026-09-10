<?php
/**
 * app/Models/Stage.php
 * Toutes les requêtes SQL concernant la table `stages`.
 * Un stage est créé automatiquement quand le RH accepte une demande.
 */

class Stage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Crée un nouveau stage et renvoie son id.
     */
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
            'stagiaire_id'   => $data['stagiaire_id'],
            'departement_id' => $data['departement_id'],
            'section_id'     => $data['section_id'],
            'agent_id'       => $data['agent_id'],
            'date_debut'     => $data['date_debut'],
            'date_fin'       => $data['date_fin'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Renvoie le stage lié à une demande donnée (relation 1-1).
     */
    public function findByDemandeId(int $demandeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM stages WHERE demande_id = :demande_id LIMIT 1'
        );
        $stmt->execute(['demande_id' => $demandeId]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }

    /**
     * Renvoie tous les stages encadrés par un agent donné, avec les
     * infos du stagiaire (utilisé plus tard pour l'espace agent).
     */
    public function findByAgentId(int $agentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT st.*, u.nom AS stagiaire_nom, u.prenom AS stagiaire_prenom, u.email AS stagiaire_email
             FROM stages st
             JOIN users u ON st.stagiaire_id = u.id
             WHERE st.agent_id = :agent_id
             ORDER BY st.date_debut DESC'
        );
        $stmt->execute(['agent_id' => $agentId]);

        return $stmt->fetchAll();
    }

    /**
     * Renvoie un stage précis par son id.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM stages WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }
        /**
     * Renvoie le stage actif d'un stagiaire donné (le plus récent).
     * Utilisé pour que le stagiaire retrouve son propre stage afin
     * d'y ajouter des entrées de journal.
     */
    public function findByStagiaireId(int $stagiaireId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM stages
             WHERE stagiaire_id = :stagiaire_id
             ORDER BY date_debut DESC
             LIMIT 1'
        );
        $stmt->execute(['stagiaire_id' => $stagiaireId]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }
        /**
     * Renvoie un stage avec toutes les infos nécessaires à l'attestation :
     * nom du stagiaire, nom de l'agent, section, département.
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT st.*,
                    stag.nom AS stagiaire_nom, stag.prenom AS stagiaire_prenom,
                    ag.nom AS agent_nom, ag.prenom AS agent_prenom,
                    sec.nom AS section_nom, dep.nom AS departement_nom
             FROM stages st
             JOIN users stag ON st.stagiaire_id = stag.id
             JOIN users ag ON st.agent_id = ag.id
             JOIN sections sec ON st.section_id = sec.id
             JOIN departements dep ON st.departement_id = dep.id
             WHERE st.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $stage = $stmt->fetch();

        return $stage ?: null;
    }
}