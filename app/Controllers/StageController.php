<?php
/**
 * app/Controllers/StageController.php
 * Gère le dépôt d'une demande de stage, le suivi de son statut, le
 * journal de bord, et le dépôt du rapport final — côté stagiaire.
 */

class StageController
{
        private DemandeStage $demandeModel;
    private Stage $stageModel;
    private JournalStage $journalModel;
    private Rapport $rapportModel;
    private Attestation $attestationModel;

    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    private const ALLOWED_MIME  = ['application/pdf'];

    public function __construct()
    {
        $this->demandeModel     = new DemandeStage();
        $this->stageModel       = new Stage();
        $this->journalModel     = new JournalStage();
        $this->rapportModel     = new Rapport();
        $this->attestationModel = new Attestation();
    }

    /**
     * GET/POST /stage/depot
     */
    public function depot(): void
    {
        Security::requireRole('stagiaire');

        $errors = [];
        $old = [
            'nom' => '', 'prenom' => '', 'telephone' => '', 'email' => '',
            'universite' => '', 'filiere' => '', 'niveau_etude' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $data = Security::sanitizeArray($_POST);
            $old = $data;

            foreach (['nom', 'prenom', 'telephone', 'email', 'universite', 'filiere', 'niveau_etude'] as $champ) {
                if (empty($data[$champ])) {
                    $errors[] = "Le champ « {$champ} » est obligatoire.";
                }
            }
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email est invalide.";
            }

            $cvPath = null;
            if (empty($_FILES['cv']['name'])) {
                $errors[] = "Le CV est obligatoire (format PDF).";
            } else {
                $cvPath = $this->handleUpload($_FILES['cv'], 'cv', $errors);
            }

            $lettrePath = null;
            if (!empty($_FILES['lettre_motivation']['name'])) {
                $lettrePath = $this->handleUpload($_FILES['lettre_motivation'], 'lettres', $errors);
            }

            if (empty($errors)) {
                $this->demandeModel->create([
                    'stagiaire_id'           => Security::currentUserId(),
                    'nom'                    => $data['nom'],
                    'prenom'                 => $data['prenom'],
                    'telephone'              => $data['telephone'],
                    'email'                  => $data['email'],
                    'universite'             => $data['universite'],
                    'filiere'                => $data['filiere'],
                    'niveau_etude'           => $data['niveau_etude'],
                    'cv_path'                => $cvPath,
                    'lettre_motivation_path' => $lettrePath,
                ]);

                header('Location: /stage/suivi');
                exit;
            }
        }

        $this->render('stage/depot', ['errors' => $errors, 'old' => $old]);
    }

    /**
     * GET /stage/suivi
     */
    public function suivi(): void
    {
        Security::requireRole('stagiaire');

        $demandes = $this->demandeModel->findByStagiaireId(Security::currentUserId());

        $this->render('stage/suivi', ['demandes' => $demandes]);
    }

    /**
     * GET/POST /stage/journal
     */
    public function journal(): void
    {
        Security::requireRole('stagiaire');

        $stage = $this->stageModel->findByStagiaireId(Security::currentUserId());
        $errors = [];

        if (!$stage) {
            $this->render('stage/journal', ['stage' => null, 'entrees' => [], 'errors' => []]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $data = Security::sanitizeArray($_POST);

            if (empty($data['date_entree'])) $errors[] = "La date est obligatoire.";
            if (empty($data['activites']))   $errors[] = "Les activités sont obligatoires.";

            $pieceJointe = null;
            if (!empty($_FILES['piece_jointe']['name'])) {
                $pieceJointe = $this->handleUpload($_FILES['piece_jointe'], 'journaux', $errors);
            }

            if (empty($errors)) {
                $this->journalModel->create([
                    'stage_id'             => $stage['id'],
                    'date_entree'          => $data['date_entree'],
                    'activites'            => $data['activites'],
                    'difficultes'          => $data['difficultes'] ?? null,
                    'competences_acquises' => $data['competences_acquises'] ?? null,
                    'piece_jointe'         => $pieceJointe,
                ]);

                header('Location: /stage/journal');
                exit;
            }
        }

        $entrees = $this->journalModel->findByStageId($stage['id']);

        $this->render('stage/journal', ['stage' => $stage, 'entrees' => $entrees, 'errors' => $errors]);
    }

    /**
     * GET/POST /stage/rapport
     * Le stagiaire dépose (ou re-dépose si refusé) son rapport final.
     */
    public function rapport(): void
    {
        Security::requireRole('stagiaire');

        $stage = $this->stageModel->findByStagiaireId(Security::currentUserId());
        $errors = [];

        if (!$stage) {
            $this->render('stage/rapport', ['stage' => null, 'rapport' => null, 'errors' => []]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            if (empty($_FILES['rapport']['name'])) {
                $errors[] = "Le fichier du rapport est obligatoire (PDF).";
            } else {
                $rapportPath = $this->handleUpload($_FILES['rapport'], 'rapports', $errors);

                if (empty($errors)) {
                    $this->rapportModel->deposer($stage['id'], $rapportPath, 'pdf');
                    header('Location: /stage/rapport');
                    exit;
                }
            }
        }

        $rapport = $this->rapportModel->findByStageId($stage['id']);

        $this->render('stage/rapport', ['stage' => $stage, 'rapport' => $rapport, 'errors' => $errors]);
    }
    /**
     * GET /stage/attestation
     */
    public function attestation(): void
    {
        Security::requireRole('stagiaire');

        $stage = $this->stageModel->findByStagiaireId(Security::currentUserId());
        $attestation = $stage ? $this->attestationModel->findByStageId($stage['id']) : null;

        $this->render('stage/attestation', [
            'stage'       => $stage,
            'attestation' => $attestation,
        ]);
    }

    /**
     * GET /stage/attestation/telecharger
     */
    public function telechargerAttestation(): void
    {
        Security::requireRole('stagiaire');

        $stage = $this->stageModel->findByStagiaireId(Security::currentUserId());
        if (!$stage) {
            http_response_code(404);
            echo "Aucun stage associé à votre compte.";
            return;
        }

        $attestation = $this->attestationModel->findByStageId($stage['id']);
        if (!$attestation) {
            http_response_code(404);
            echo "Votre attestation n'a pas encore été générée.";
            return;
        }

        $cheminAbsolu = dirname(__DIR__, 2) . '/' . $attestation['fichier_path'];
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
    private function handleUpload(array $file, string $dossier, array &$errors): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'envoi du fichier.";
            return null;
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            $errors[] = "Le fichier ne doit pas dépasser 5 Mo.";
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            $errors[] = "Seuls les fichiers PDF sont acceptés.";
            return null;
        }

        $nomFichier = uniqid($dossier . '_', true) . '.pdf';
        $cheminDestination = dirname(__DIR__, 2) . '/public/uploads/' . $dossier . '/' . $nomFichier;

        if (!move_uploaded_file($file['tmp_name'], $cheminDestination)) {
            $errors[] = "Impossible d'enregistrer le fichier, veuillez réessayer.";
            return null;
        }

        return 'uploads/' . $dossier . '/' . $nomFichier;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
}