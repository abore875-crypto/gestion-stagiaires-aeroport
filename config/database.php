<?php
/**
 * config/database.php
 * Connexion à la base de données MySQL (PDO, Singleton).
 */

class Database
{
    private static string $host    = 'localhost';
    private static string $dbname  = 'gestion_stagiaires_aeroport';
    private static string $user    = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::$host
                 . ';dbname=' . self::$dbname
                 . ';charset=' . self::$charset;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$password, $options);
            } catch (PDOException $e) {
                error_log('Erreur de connexion BDD : ' . $e->getMessage());
                die('Erreur de connexion à la base de données. Veuillez réessayer plus tard.');
            }
        }

        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}