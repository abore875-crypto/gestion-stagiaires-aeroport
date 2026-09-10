<?php
/**
 * core/Security.php
 * Regroupe tout ce qui touche à la sécurité : démarrage sécurisé de la
 * session, nettoyage des entrées utilisateur, génération et vérification
 * des jetons CSRF, et quelques helpers d'authentification.
 */

class Security
{
    /**
     * Démarre une session PHP avec des réglages de cookie sécurisés.
     * À appeler une seule fois, tout en haut du routeur (public/index.php).
     */
    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Lax');
            // Mettre à 1 uniquement si le site tourne en HTTPS
            ini_set('session.cookie_secure', 0);

            session_start();
        }
    }

    /**
     * Nettoie une chaîne de caractères venant d'un formulaire :
     * supprime les espaces inutiles et échappe les caractères HTML
     * pour éviter les failles XSS à l'affichage.
     */
       public static function sanitize(?string $value): string
    {
        return trim($value ?? '');
    }

    /**
     * Nettoie récursivement un tableau (ex: $_POST tout entier).
     */
    public static function sanitizeArray(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = is_array($value)
                ? self::sanitizeArray($value)
                : self::sanitize($value);
        }
        return $clean;
    }

    // -------------------------------------------------------------
    // Protection CSRF
    // -------------------------------------------------------------

    /**
     * Génère (ou réutilise) un jeton CSRF stocké en session,
     * à insérer dans un champ caché de chaque formulaire :
     * <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le jeton CSRF envoyé par un formulaire.
     * Retourne false si le jeton est absent, invalide ou expiré.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // -------------------------------------------------------------
    // Helpers d'authentification / autorisation
    // -------------------------------------------------------------

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function currentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function currentUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Coupe l'exécution et redirige si l'utilisateur n'est pas connecté.
     */
    public static function requireLogin(string $redirectTo = '/connexion'): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    /**
     * Coupe l'exécution si l'utilisateur connecté n'a pas le bon rôle.
     * Exemple : Security::requireRole('rh');
     */
    public static function requireRole(string $role, string $redirectTo = '/'): void
    {
        self::requireLogin();
        if (self::currentUserRole() !== $role) {
            http_response_code(403);
            echo "Accès refusé : cette page est réservée au rôle « {$role} ».";
            exit;
        }
    }
}