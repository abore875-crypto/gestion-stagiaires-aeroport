<?php
/**
 * app/Controllers/AgentController.php
 * Espace de l'agent encadrant : liste de ses stagiaires, validation du
 * journal, et validation du rapport final. Réservé au rôle 'agent'.
 */

class AgentController
{
    private Stage $stageModel;
    private JournalStage $journalModel;
    private User $userModel;
    private Rapport $rapportModel;

    public function __construct()
    {
        $this->stageModel   = new Stage();
        $this->journalModel = new JournalStage();
        $this->userModel    = new User();
        $this->rapportModel = new Rapport();
    }

    /**
     * GET /agent/tableau-de-bord
     */
    public function dashboard(): void
    {
        Security::requireRole('agent');

        $stages = $this->stageModel->findByAgentId(Security::currentUserId());

        $this->render('agent/dashboard', ['stages' => $stages]);
    }

    /**
     * GET/POST /agent/journal?stage_id=X
     */
    public function journal(): void
    {
        Security::requireRole('agent');

        $stageId = (int) ($_GET['stage_id'] ?? $_POST['stage_id'] ?? 0);
        $stage = $this->stageModel->findById($stageId);

        if (!$stage || (int) $stage['agent_id'] !== Security::currentUserId()) {
            http_response_code(403);
            echo "Accès refusé : ce stage ne vous est pas assigné.";
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            } else {
                $journalId   = (int) ($_POST['journal_id'] ?? 0);
                $commentaire = Security::sanitize($_POST['commentaire_agent'] ?? '');
                $noteBrute   = trim($_POST['note_evaluation'] ?? '');
                $note        = $noteBrute === '' ? null : (float) $noteBrute;

                $this->journalModel->valider($journalId, $commentaire ?: null, $note);
                header('Location: /agent/journal?stage_id=' . $stageId);
                exit;
            }
        }

        $stagiaire = $this->userModel->findById($stage['stagiaire_id']);
        $entrees   = $this->journalModel->findByStageId($stageId);

        $this->render('agent/journal', ['stage' => $stage, 'stagiaire' => $stagiaire, 'entrees' => $entrees, 'errors' => $errors]);
    }

    /**
     * GET/POST /agent/rapport?stage_id=X
     * L'agent consulte et valide (ou refuse) le rapport final du stagiaire.
     */
    public function rapport(): void
    {
        Security::requireRole('agent');

        $stageId = (int) ($_GET['stage_id'] ?? $_POST['stage_id'] ?? 0);
        $stage = $this->stageModel->findById($stageId);

        if (!$stage || (int) $stage['agent_id'] !== Security::currentUserId()) {
            http_response_code(403);
            echo "Accès refusé : ce stage ne vous est pas assigné.";
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            } else {
                $rapportId   = (int) ($_POST['rapport_id'] ?? 0);
                $action      = $_POST['action'] ?? '';
                $commentaire = Security::sanitize($_POST['commentaire'] ?? '') ?: null;

                if ($action === 'valider') {
                    $this->rapportModel->validerParAgent($rapportId, $commentaire);
                } elseif ($action === 'refuser') {
                    $this->rapportModel->refuser($rapportId, 'agent', $commentaire);
                }

                header('Location: /agent/rapport?stage_id=' . $stageId);
                exit;
            }
        }

        $stagiaire = $this->userModel->findById($stage['stagiaire_id']);
        $rapport   = $this->rapportModel->findByStageId($stageId);

        $this->render('agent/rapport', ['stage' => $stage, 'stagiaire' => $stagiaire, 'rapport' => $rapport, 'errors' => $errors]);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
}