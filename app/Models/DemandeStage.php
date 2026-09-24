<?php
/**
 * app/Models/DemandeStage.php
 * Toutes les requêtes SQL concernant la table `demandes_stage`.
 * Une demande peut désormais exister SANS compte stagiaire associé
 * (dépôt public) : stagiaire_id est nullable, rempli plus tard quand
 * le candidat crée son compte après acceptation.
 */

class DemandeStage
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Crée une nouvelle demande. stagiaire_id est optionnel : null pour
     * un dépôt public (candidat sans compte), rempli pour un dépôt
     * effectué par un stagiaire déjà connecté (cas encore possible).
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
            'stagiaire_id'            => $data['stagiaire_id'] ?? null,
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

    public function findByStagiaireId(int $stagiaireId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM demandes_stage WHERE stagiaire_id = :stagiaire_id ORDER BY date_demande DESC'
        );
        $stmt->execute(['stagiaire_id' => $stagiaireId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM demandes_stage WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $d = $stmt->fetch();
        return $d ?: null;
    }

    /**
     * Recherche publique (sans compte) : le candidat retrouve SES
     * demandes en donnant l'email ET le téléphone utilisés au dépôt.
     * Exiger les deux réduit le risque qu'un tiers devine juste l'email.
     */
    public function findByEmailAndTelephone(string $email, string $telephone): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM demandes_stage WHERE email = :email AND telephone = :telephone ORDER BY date_demande DESC'
        );
        $stmt->execute(['email' => $email, 'telephone' => $telephone]);
        return $stmt->fetchAll();
    }

    /**
     * Relie automatiquement toutes les demandes sans compte (stagiaire_id
     * NULL) qui portent cet email à un compte fraîchement créé. Renvoie
     * les ids des demandes liées (utile pour relier aussi leurs stages).
     */
    public function linkToStagiaireByEmail(string $email, int $stagiaireId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM demandes_stage WHERE email = :email AND stagiaire_id IS NULL'
        );
        $stmt->execute(['email' => $email]);
        $ids = array_column($stmt->fetchAll(), 'id');

        if (!empty($ids)) {
            $update = $this->db->prepare(
                'UPDATE demandes_stage SET stagiaire_id = :stagiaire_id WHERE email = :email AND stagiaire_id IS NULL'
            );
            $update->execute(['stagiaire_id' => $stagiaireId, 'email' => $email]);
        }

        return $ids;
    }

    public function findAll(?string $statut = null): array
    {
        if ($statut) {
            $stmt = $this->db->prepare('SELECT * FROM demandes_stage WHERE statut = :statut ORDER BY date_demande DESC');
            $stmt->execute(['statut' => $statut]);
        } else {
            $stmt = $this->db->query('SELECT * FROM demandes_stage ORDER BY date_demande DESC');
        }
        return $stmt->fetchAll();
    }

    public function countByStatut(): array
    {
        $stmt = $this->db->query('SELECT statut, COUNT(*) AS total FROM demandes_stage GROUP BY statut');
        $counts = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
        foreach ($stmt->fetchAll() as $ligne) {
            $counts[$ligne['statut']] = (int) $ligne['total'];
        }
        return $counts;
    }

    public function updateStatut(int $id, string $statut, int $traitePar, ?string $motifRefus = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE demandes_stage
             SET statut = :statut, traite_par = :traite_par, date_traitement = NOW(), motif_refus = :motif_refus
             WHERE id = :id'
        );
        return $stmt->execute(['statut' => $statut, 'traite_par' => $traitePar, 'motif_refus' => $motifRefus, 'id' => $id]);
    }
}