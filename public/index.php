<?php
/**
 * public/index.php
 * Routeur unique de l'application. Toutes les requêtes passent par ici
 * (voir .htaccess). Il associe une URL à une méthode de contrôleur,
 * puis laisse le contrôleur gérer la logique et afficher la vue.
 */

// ---------------------------------------------------------------------
// Configuration de base (affichage des erreurs en dev, fuseau horaire)
// ---------------------------------------------------------------------
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Bamako');

define('ROOT_PATH', dirname(__DIR__));

// ---------------------------------------------------------------------
// Autoload simple des classes core / config / models / controllers
// ---------------------------------------------------------------------
spl_autoload_register(function ($class) {
    $dirs = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/config/',
        ROOT_PATH . '/app/Models/',
        ROOT_PATH . '/app/Controllers/',
    ];
    foreach ($dirs as $dir) {
        // On essaie d'abord le nom exact (ex: Security.php), puis en
        // minuscules (ex: database.php pour la classe Database).
        foreach ([$class . '.php', strtolower($class) . '.php'] as $filename) {
            $file = $dir . $filename;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// ---------------------------------------------------------------------
// Session sécurisée (doit démarrer avant tout affichage)
// ---------------------------------------------------------------------
Security::startSecureSession();

// ---------------------------------------------------------------------
// Table de routage : 'route' => [Controller::class, 'methode']
// ---------------------------------------------------------------------
$routes = [
    'connexion'                    => [AuthController::class, 'login'],
    'inscription'                  => [AuthController::class, 'register'],
    'deconnexion'                  => [AuthController::class, 'logout'],
    'demande/deposer'              => [DemandeController::class, 'deposer'],
    'demande/suivi'                => [DemandeController::class, 'suivi'],
    'stage/depot'                  => [StageController::class, 'depot'],
    'stage/suivi'                  => [StageController::class, 'suivi'],
    'stage/journal'                => [StageController::class, 'journal'],
    'stage/rapport'                => [StageController::class, 'rapport'],
    'stage/attestation'            => [StageController::class, 'attestation'],
    'stage/attestation/telecharger' => [StageController::class, 'telechargerAttestation'],
    'rh/tableau-de-bord'           => [RhController::class, 'dashboard'],
    'rh/agents'                    => [RhController::class, 'agents'],
    'rh/rapports'                  => [RhController::class, 'rapports'],
    'rh/attestations'              => [RhController::class, 'attestations'],
    'rh/attestations/telecharger'  => [RhController::class, 'telechargerAttestation'],
    'rh/notes-service'             => [RhController::class, 'notesService'],
    'rh/notes-service/telecharger' => [RhController::class, 'telechargerNoteService'],
    'rh/candidat'                  => [RhController::class, 'candidat'],
    'agent/tableau-de-bord'        => [AgentController::class, 'dashboard'],
    'agent/journal'                => [AgentController::class, 'journal'],
    'agent/rapport'                => [AgentController::class, 'rapport'],
    'agent/note-service/telecharger' => [AgentController::class, 'telechargerNoteService'],
];

// ---------------------------------------------------------------------
// Récupération de la route demandée à partir de l'URL
// ---------------------------------------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = trim($uri, '/');

// Page d'accueil
if ($route === '') {
    require ROOT_PATH . '/app/Views/home.php';
    exit;
}

// Route connue -> on instancie le contrôleur et on appelle la méthode
if (isset($routes[$route])) {
    [$controllerClass, $methode] = $routes[$route];
    $controller = new $controllerClass();
    $controller->$methode();
    exit;
}

// Aucune route ne correspond -> 404
http_response_code(404);
$titrePage = 'Page introuvable';
require ROOT_PATH . '/app/Views/partials/header.php';
echo '<div class="text-center py-5"><h1 class="h3 fw-medium">404</h1><p class="text-secondary">Cette page n\'existe pas.</p><a href="/" class="btn btn-primary mt-3">Retour à l\'accueil</a></div>';
require ROOT_PATH . '/app/Views/partials/footer.php';