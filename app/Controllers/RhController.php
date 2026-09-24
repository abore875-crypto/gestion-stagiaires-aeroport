<?php
/**
 * app/Controllers/RhController.php
 * Tableau de bord RH : demandes de stage, gestion des agents, et
 * validation finale des rapports. Réservé au rôle 'rh'.
 */

class RhController
{
    private DemandeStage $demandeModel;
    private User $userModel;
    private Section $sectionModel;
    private Stage $stageModel;
    private Rapport $rapportModel;
    private Attestation $attestationModel;
    private NoteService $noteServiceModel;
    private JournalStage $journalModel;

    public function __construct()
    {
        $this->demandeModel    = new DemandeStage();
        $this->userModel       = new User();
        $this->sectionModel    = new Section();
        $this->stageModel      = new Stage();
        $this->rapportModel    = new Rapport();
        $this->attestationModel = new Attestation();
        $this->noteServiceModel = new NoteService();
        $this->journalModel    = new JournalStage();
    }

    /**
     * GET/POST /rh/tableau-de-bord
     */
    public function dashboard(): void
    {
        Security::requireRole('rh');

        $errors = [];
        $agents = $this->userModel->findAgentsWithSection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            } else {
                $demandeId = (int) ($_POST['demande_id'] ?? 0);
                $action    = $_POST['action'] ?? '';

                if ($action === 'accepter') {
                    $errors = $this->accepterDemande($demandeId, $_POST, $agents);
                } elseif ($action === 'refuser') {
                    $motif = Security::sanitize($_POST['motif_refus'] ?? '');
                    $this->demandeModel->updateStatut($demandeId, 'refusee', Security::currentUserId(), $motif);
                } else {
                    $errors[] = "Action inconnue.";
                }

                if (empty($errors)) {
                    header('Location: /rh/tableau-de-bord');
                    exit;
                }
            }
        }

        $statutFiltre = $_GET['statut'] ?? null;
        $statutsValides = ['en_attente', 'acceptee', 'refusee'];
        if ($statutFiltre !== null && !in_array($statutFiltre, $statutsValides, true)) {
            $statutFiltre = null;
        }

        $demandes = $this->demandeModel->findAll($statutFiltre);

               $this->render('rh/dashboard', [
            'demandes' => $demandes, 'statutFiltre' => $statutFiltre, 'errors' => $errors,
            'agents' => $agents, 'stats' => $this->demandeModel->countByStatut(),
        ]);
    }

    private function accepterDemande(int $demandeId, array $post, array $agents): array
    {
        $errors = [];
        $demande = $this->demandeModel->findById($demandeId);
        if (!$demande) return ["Demande introuvable."];

        $agentId   = (int) ($post['agent_id'] ?? 0);
        $dateDebut = Security::sanitize($post['date_debut'] ?? '');
        $dateFin   = Security::sanitize($post['date_fin'] ?? '');

        $agent = null;
        foreach ($agents as $a) {
            if ((int) $a['id'] === $agentId) { $agent = $a; break; }
        }

        if (!$agent) {
            $errors[] = "Veuillez choisir un agent encadrant valide.";
        } elseif (empty($agent['section_id'])) {
            $errors[] = "Cet agent n'a pas de section assignée.";
        }

        if (empty($dateDebut) || empty($dateFin)) {
            $errors[] = "Les dates de début et de fin sont obligatoires.";
        } elseif ($dateFin <= $dateDebut) {
            $errors[] = "La date de fin doit être après la date de début.";
        }

        if (!empty($errors)) return $errors;

               $stageId = $this->stageModel->create([
            'demande_id' => $demandeId, 'stagiaire_id' => $demande['stagiaire_id'],
            'departement_id' => $agent['departement_id'], 'section_id' => $agent['section_id'],
            'agent_id' => $agent['id'], 'date_debut' => $dateDebut, 'date_fin' => $dateFin,
        ]);

        $this->demandeModel->updateStatut($demandeId, 'acceptee', Security::currentUserId());
        $this->genererNoteService($stageId);

        return [];
    }

    /**
     * Génère automatiquement la note de service (mémo interne) dès
     * qu'un stage est créé, pour informer le département concerné.
     */
    private function genererNoteService(int $stageId): void
    {
        if ($this->noteServiceModel->findByStageId($stageId)) {
            return;
        }

        $stage = $this->stageModel->findByIdWithDetails($stageId);
        if (!$stage) {
            return;
        }

        $nomFichier = 'note_service_' . $stageId . '_' . uniqid() . '.pdf';
        $cheminAbsolu = dirname(__DIR__, 2) . '/storage/pdf/notes_service/' . $nomFichier;

        PdfGenerator::generateFromView(
            dirname(__DIR__) . '/Views/pdf/note_service.php',
            ['stage' => $stage],
            $cheminAbsolu
        );

        $this->noteServiceModel->create($stageId, 'storage/pdf/notes_service/' . $nomFichier);
    }

    /**
     * GET/POST /rh/agents
     */
    public function agents(): void
    {
        Security::requireRole('rh');

        $errors = [];
        $old = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '', 'section_id' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $data = Security::sanitizeArray($_POST);
            $old = $data;

            if (empty($data['nom']))    $errors[] = "Le nom est obligatoire.";
            if (empty($data['prenom'])) $errors[] = "Le prénom est obligatoire.";
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email est invalide.";
            }
            if (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
            if (empty($errors) && $this->userModel->emailExists($data['email'])) {
                $errors[] = "Un compte existe déjà avec cet email.";
            }

            $sectionId = (int) ($data['section_id'] ?? 0);
            if (empty($errors) && !$this->sectionModel->findById($sectionId)) {
                $errors[] = "Veuillez choisir une section valide.";
            }

            if (empty($errors)) {
                $this->userModel->create([
                    'nom' => $data['nom'], 'prenom' => $data['prenom'], 'email' => $data['email'],
                    'telephone' => $data['telephone'] ?? null,
                    'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
                    'role' => 'agent', 'section_id' => $sectionId,
                ]);
                header('Location: /rh/agents');
                exit;
            }
        }

        $this->render('rh/agents', [
            'errors' => $errors, 'old' => $old,
            'agents' => $this->userModel->findAgentsWithSection(),
            'sections' => $this->sectionModel->findAllWithDepartement(),
        ]);
    }

    /**
     * GET/POST /rh/rapports
     * Liste les rapports validés par un agent, en attente de la
     * validation finale du RH. Déclenche (plus tard) l'attestation.
     */
    public function rapports(): void
    {
        Security::requireRole('rh');

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            } else {
                $rapportId   = (int) ($_POST['rapport_id'] ?? 0);
                $action      = $_POST['action'] ?? '';
                $commentaire = Security::sanitize($_POST['commentaire'] ?? '') ?: null;

                                if ($action === 'valider') {
                    $this->rapportModel->validerParRh($rapportId, $commentaire);
                    $rapport = $this->rapportModel->findById($rapportId);
                    $this->genererAttestation((int) $rapport['stage_id']);
                } elseif ($action === 'refuser') {
                    $this->rapportModel->refuser($rapportId, 'rh', $commentaire);
                }

                header('Location: /rh/rapports');
                exit;
            }
        }

        $this->render('rh/rapports', [
            'rapports' => $this->rapportModel->findEnAttenteRh(),
            'errors'   => $errors,
        ]);
    }

    /**
     * Génère le PDF d'attestation pour un stage et l'enregistre en base.
     * Si une attestation existe déjà pour ce stage, on ne la régénère
     * pas — évite les doublons de fichiers.
     */
    private function genererAttestation(int $stageId): void
    {
        if ($this->attestationModel->findByStageId($stageId)) {
            return;
        }

        $stage = $this->stageModel->findByIdWithDetails($stageId);
        if (!$stage) {
            return;
        }

        $nomFichier = 'attestation_' . $stageId . '_' . uniqid() . '.pdf';
        $cheminAbsolu = dirname(__DIR__, 2) . '/storage/pdf/attestations/' . $nomFichier;

        PdfGenerator::generateFromView(
            dirname(__DIR__) . '/Views/pdf/attestation.php',
            ['stage' => $stage],
            $cheminAbsolu
        );

        $this->attestationModel->create($stageId, 'storage/pdf/attestations/' . $nomFichier);
    }

    /**
     * GET /rh/attestations
     */
    public function attestations(): void
    {
        Security::requireRole('rh');

        $this->render('rh/attestations', [
            'attestations' => $this->attestationModel->findAllWithStagiaire(),
        ]);
    }

    /**
     * GET /rh/attestations/telecharger?stage_id=X
     */
    public function telechargerAttestation(): void
    {
        Security::requireRole('rh');

        $stageId = (int) ($_GET['stage_id'] ?? 0);
        $attestation = $this->attestationModel->findByStageId($stageId);

        if (!$attestation) {
            http_response_code(404);
            echo "Attestation introuvable.";
            return;
        }

        $this->envoyerFichierPdf($attestation['fichier_path']);
    }

    private function envoyerFichierPdf(string $cheminRelatif): void
    {
        $cheminAbsolu = dirname(__DIR__, 2) . '/' . $cheminRelatif;

        if (!file_exists($cheminAbsolu)) {
            http_response_code(404);
            echo "Fichier introuvable.";
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="attestation.pdf"');
        header('Content-Length: ' . filesize($cheminAbsolu));
        readfile($cheminAbsolu);
        exit;
    }
        /**
     * GET /rh/notes-service
     */
    public function notesService(): void
    {
        Security::requireRole('rh');

        $this->render('rh/notes-service', [
            'notes' => $this->noteServiceModel->findAllWithDetails(),
        ]);
    }

    /**
     * GET /rh/notes-service/telecharger?stage_id=X
     */
    public function telechargerNoteService(): void
    {
        Security::requireRole('rh');

        $stageId = (int) ($_GET['stage_id'] ?? 0);
        $note = $this->noteServiceModel->findByStageId($stageId);

        if (!$note) {
            http_response_code(404);
            echo "Note de service introuvable.";
            return;
        }

        $this->envoyerFichierPdf($note['fichier_path']);
    }
    /**
     * GET /rh/candidat?demande_id=X
     * Fiche complète d'un candidat : sa demande, et si elle a été
     * acceptée, son stage, son journal, son rapport et son attestation.
     */
    public function candidat(): void
    {
        Security::requireRole('rh');

        $demandeId = (int) ($_GET['demande_id'] ?? 0);
        $demande = $this->demandeModel->findById($demandeId);

        if (!$demande) {
            http_response_code(404);
            echo "Candidat introuvable.";
            return;
        }

        $stage = null;
        $entrees = [];
        $rapport = null;
        $attestation = null;

        if ($demande['statut'] === 'acceptee') {
            $stageBrut = $this->stageModel->findByDemandeId($demandeId);
            if ($stageBrut) {
                $stage = $this->stageModel->findByIdWithDetails($stageBrut['id']);
                $entrees     = $this->journalModel->findByStageId($stage['id']);
                $rapport     = $this->rapportModel->findByStageId($stage['id']);
                $attestation = $this->attestationModel->findByStageId($stage['id']);
            }
        }

        $this->render('rh/candidat', [
            'demande'     => $demande,
            'stage'       => $stage,
            'entrees'     => $entrees,
            'rapport'     => $rapport,
            'attestation' => $attestation,
        ]);
    }
    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
}