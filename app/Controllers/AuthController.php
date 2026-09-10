<?php
/**
 * app/Controllers/AuthController.php
 * Gère l'inscription (stagiaire uniquement), la connexion et la
 * déconnexion. Le contrôleur orchestre : il lit la requête, appelle le
 * modèle, puis affiche la vue. Il ne contient pas de SQL ni de HTML.
 */

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * GET/POST /inscription
     * Formulaire public de création de compte — réservé aux stagiaires.
     * RH et agents ne peuvent jamais être créés depuis cette page.
     */
    public function register(): void
    {
        $errors = [];
        $old = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $data = Security::sanitizeArray($_POST);
            $old = $data;

            // --- Validation ------------------------------------------------
            if (empty($data['nom']))    $errors[] = "Le nom est obligatoire.";
            if (empty($data['prenom'])) $errors[] = "Le prénom est obligatoire.";
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email est invalide.";
            }
            if (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
            if (($data['mot_de_passe'] ?? '') !== ($data['confirmation'] ?? '')) {
                $errors[] = "Les deux mots de passe ne correspondent pas.";
            }
            if (empty($errors) && $this->userModel->emailExists($data['email'])) {
                $errors[] = "Un compte existe déjà avec cet email.";
            }

            // --- Création ----------------------------------------------------
            if (empty($errors)) {
                $userId = $this->userModel->create([
                    'nom'          => $data['nom'],
                    'prenom'       => $data['prenom'],
                    'email'        => $data['email'],
                    'telephone'    => $data['telephone'] ?? null,
                    'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
                    'role'         => 'stagiaire', // forcé : jamais choisi par l'utilisateur
                ]);

                $this->logUserIn($userId);
                header('Location: /stage/depot');
                exit;
            }
        }

        $this->render('auth/register', [
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    /**
     * GET/POST /connexion
     */
    public function login(): void
    {
        $errors = [];
        $old = ['email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide, veuillez réessayer.";
            }

            $email = Security::sanitize($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $old['email'] = $email;

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
                    $errors[] = "Email ou mot de passe incorrect.";
                } elseif (!$user['actif']) {
                    $errors[] = "Ce compte a été désactivé. Contactez le service RH.";
                } else {
                    $this->logUserIn($user['id'], $user['role']);
                    $this->userModel->updateLastLogin($user['id']);
                    header('Location: ' . $this->redirectAfterLogin($user['role']));
                    exit;
                }
            }
        }

        $this->render('auth/login', [
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    /**
     * GET /deconnexion
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /connexion');
        exit;
    }

    // -------------------------------------------------------------
    // Helpers privés
    // -------------------------------------------------------------

    private function logUserIn(int $userId, ?string $role = 'stagiaire'): void
    {
        session_regenerate_id(true); // empêche la fixation de session
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_role'] = $role;
    }

    private function redirectAfterLogin(string $role): string
    {
        return match ($role) {
            'rh'    => '/rh/tableau-de-bord',
            'agent' => '/agent/tableau-de-bord',
            default => '/stage/suivi',
        };
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
}