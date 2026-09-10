<?php
/**
 * app/Models/User.php
 * Toutes les requêtes SQL concernant la table `users`.
 * Un modèle ne contient JAMAIS de HTML ni de logique de page :
 * seulement des accès à la base de données.
 */

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Recherche un utilisateur par son email (utilisé à la connexion).
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Recherche un utilisateur par son id.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Vérifie si un email est déjà utilisé (pour l'inscription).
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    /**
     * Crée un nouvel utilisateur et renvoie son id.
     * Le mot de passe DOIT déjà être haché avant d'arriver ici
     * (voir AuthController::register()).
     * section_id n'est utilisé que pour les agents ; null sinon.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (nom, prenom, email, telephone, mot_de_passe, role, section_id)
             VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role, :section_id)'
        );

        $stmt->execute([
            'nom'          => $data['nom'],
            'prenom'       => $data['prenom'],
            'email'        => $data['email'],
            'telephone'    => $data['telephone'] ?? null,
            'mot_de_passe' => $data['mot_de_passe'],
            'role'         => $data['role'],
            'section_id'   => $data['section_id'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour la date de dernière connexion.
     */
    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET derniere_connexion = NOW() WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    /**
     * Liste tous les utilisateurs d'un rôle donné (ex: pour le RH qui
     * veut voir la liste des agents).
     */
    public function findAllByRole(string $role): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nom, prenom, email, telephone, section_id, actif, created_at
             FROM users WHERE role = :role ORDER BY nom, prenom'
        );
        $stmt->execute(['role' => $role]);

        return $stmt->fetchAll();
    }

    /**
     * Liste les agents avec le nom de leur section et de leur
     * département (pour l'affichage dans le tableau RH).
     */
       public function findAgentsWithSection(): array
    {
        $stmt = $this->db->query(
            'SELECT u.id, u.nom, u.prenom, u.email, u.telephone, u.actif, u.created_at,
                    s.id AS section_id, s.nom AS section_nom,
                    d.id AS departement_id, d.nom AS departement_nom
             FROM users u
             LEFT JOIN sections s ON u.section_id = s.id
             LEFT JOIN departements d ON s.departement_id = d.id
             WHERE u.role = \'agent\'
             ORDER BY u.nom, u.prenom'
        );

        return $stmt->fetchAll();
    }
}