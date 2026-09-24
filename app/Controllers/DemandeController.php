<?php
/**
 * app/Controllers/DemandeController.php
 * Parcours PUBLIC (sans connexion) : un candidat dépose sa demande de
 * stage directement, sans créer de compte au préalable, puis peut
 * suivre son statut avec son email + téléphone. Le compte stagiaire
 * n'est créé qu'après acceptation (voir AuthController::register()).
 */

class DemandeController
{
    private DemandeStage $demandeModel;

    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    private const ALLOWED_MIME  = ['application/pdf'];

    public function __construct()
    {
        $this->demandeModel = new DemandeStage();
    }

    /**
     * GET/POST /demande/deposer
     * Formulaire public de dépôt. Aucune connexion requise.
     */
    public function deposer(): void
    {
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
                    'stagiaire_id'           => null, // dépôt public : pas encore de compte
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

                header('Location: /demande/suivi?depose=1');
                exit;
            }
        }

        $this->render('demande/deposer', ['errors' => $errors, 'old' => $old]);
    }

    /**
     * GET/POST /demande/suivi
     * Le candidat entre son email + téléphone pour voir le statut de
     * sa/ses demande(s), sans avoir besoin d'un compte.
     */
    public function suivi(): void
    {
        $errors = [];
        $demandes = null;
        $vientDeDeposer = isset($_GET['depose']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $email = Security::sanitize($_POST['email'] ?? '');
            $telephone = Security::sanitize($_POST['telephone'] ?? '');

            if (empty($email) || empty($telephone)) {
                $errors[] = "Merci de renseigner l'email et le téléphone utilisés lors du dépôt.";
            }

            if (empty($errors)) {
                $demandes = $this->demandeModel->findByEmailAndTelephone($email, $telephone);
                if (empty($demandes)) {
                    $errors[] = "Aucune demande trouvée avec ces informations. Vérifiez l'email et le téléphone saisis.";
                }
            }
        }

        $this->render('demande/suivi', [
            'errors'         => $errors,
            'demandes'       => $demandes,
            'vientDeDeposer' => $vientDeDeposer,
        ]);
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