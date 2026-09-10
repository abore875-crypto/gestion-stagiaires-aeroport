<?php
/**
 * app/Models/DemandeStage.php
 * Toutes les requêtes SQL concernant la table `demandes_stage`.
 */

class DemandeStage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Crée une nouvelle demande de stage et renvoie son id.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO demandes_stage
                (stagiaire_id, nom, prenom, telephone, email, universite,
                 filiere, niveau_etude, cv_path, lettre_motivation_path)
             VALUES
                (:stagiaire_id, :nom, :prenom, :telephone, :email, :universite,
                 :filiere, :niveau_etude, :cv_path, :lettre_motivation_path)'
        );

        $stmt->execute([
            'stagiaire_id'            => $data['stagiaire_id'],
            'nom'                     => $data['nom'],
            'prenom'                  => $data['prenom'],
            'telephone'               => $data['telephone'],
            'email'                   => $data['email'],
            'universite'              => $data['universite'],
            'filiere'                 => $data['filiere'],
            'niveau_etude'            => $data['niveau_etude'],
            'cv_path'                 => $data['cv_path'],
            'lettre_motivation_path'  => $data['lettre_motivation_path'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Renvoie toutes les demandes déposées par un stagiaire donné,
     * de la plus récente à la plus ancienne (pour la page "suivi").
     */
    public function findByStagiaireId(int $stagiaireId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM demandes_stage
             WHERE stagiaire_id = :stagiaire_id
             ORDER BY date_demande DESC'
        );
        $stmt->execute(['stagiaire_id' => $stagiaireId]);

        return $stmt->fetchAll();
    }

    /**
     * Renvoie une demande précise par son id.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM demandes_stage WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $demande = $stmt->fetch();

        return $demande ?: null;
    }

    /**
     * Liste les demandes pour le RH, avec filtre optionnel par statut.
     */
        /**
     * Compte les demandes par statut, pour les cartes de statistiques
     * du tableau de bord RH.
     */
    public function countByStatut(): array
    {
        $stmt = $this->db->query(
            'SELECT statut, COUNT(*) AS total FROM demandes_stage GROUP BY statut'
        );

        $counts = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
        foreach ($stmt->fetchAll() as $ligne) {
            $counts[$ligne['statut']] = (int) $ligne['total'];
        }

        return $counts;
    }
    public function findAll(?string $statut = null): array
    {
        if ($statut) {
            $stmt = $this->db->prepare(
                'SELECT * FROM demandes_stage WHERE statut = :statut ORDER BY date_demande DESC'
            );
            $stmt->execute(['statut' => $statut]);
        } else {
            $stmt = $this->db->query(
                'SELECT * FROM demandes_stage ORDER BY date_demande DESC'
            );
        }

        return $stmt->fetchAll();
    }

    /**
     * Change le statut d'une demande (acceptée / refusée) et enregistre
     * qui a traité la demande et quand.
     */
    public function updateStatut(int $id, string $statut, int $traitePar, ?string $motifRefus = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE demandes_stage
             SET statut = :statut, traite_par = :traite_par,
                 date_traitement = NOW(), motif_refus = :motif_refus
             WHERE id = :id'
        );

        return $stmt->execute([
            'statut'      => $statut,
            'traite_par'  => $traitePar,
            'motif_refus' => $motifRefus,
            'id'          => $id,
        ]);
    }
}