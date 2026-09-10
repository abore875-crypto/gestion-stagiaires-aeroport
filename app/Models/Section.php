<?php
/**
 * app/Models/Section.php
 * Requêtes SQL concernant les tables `sections` et `departements`.
 */

class Section
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Renvoie toutes les sections avec le nom de leur département,
     * triées par département puis par section. Utilisé pour remplir
     * le menu déroulant "Section" du formulaire de création d'agent.
     */
    public function findAllWithDepartement(): array
    {
        $stmt = $this->db->query(
            'SELECT s.id, s.nom AS section_nom, d.id AS departement_id, d.nom AS departement_nom
             FROM sections s
             JOIN departements d ON s.departement_id = d.id
             ORDER BY d.nom, s.nom'
        );

        return $stmt->fetchAll();
    }

    /**
     * Renvoie une section précise par son id (utile pour valider
     * qu'une section existe bien avant de créer un agent).
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM sections WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $section = $stmt->fetch();

        return $section ?: null;
    }
}