<?php
/**
 * FICHIER DE CONNEXION MYSQLI UNIFIÉ POUR AIVEN
 * 
 * Ce fichier centralise TOUTES les connexions MySQLi du projet.
 * Utilise la configuration Database qui récupère depuis les variables d'environnement.
 * 
 * ⚠️ CREDENTIALS AIVEN (via variables d'environnement)
 * Host: mysql-shopfront-shopfrontoffice.b.aivencloud.com
 * Port: 22674
 * User: avnadmin
 * Password: Défini via DB_PASSWORD dans les variables d'environnement Render
 * Database: defaultdb
 * SSL: REQUIRED
 * 
 * Voir RENDER_DB_CONFIG.md pour la configuration sur Render
 */

require_once __DIR__ . '/config/database.php';

// Récupérer les paramètres de connexion Aiven depuis Database
$host = Database::getHostname();
$port = Database::getPort();
$username = Database::getUsername();
$password = Database::getPassword();
$database = Database::getDatabase();
$ssl_ca = Database::getSslCa();

// Vérifier que les paramètres sont définis
if (empty($host) || empty($database) || empty($username) || empty($password)) {
    $error_msg = "Erreur de configuration : Paramètres de connexion Aiven manquants.";
    error_log($error_msg);
    error_log("Host: " . ($host ?: 'VIDE'));
    error_log("Database: " . ($database ?: 'VIDE'));
    error_log("Username: " . ($username ?: 'VIDE'));
    error_log("Password: " . ($password ? 'DEFINI' : 'VIDE'));
    error_log("Sur Render: Vérifiez que DB_PASSWORD est défini dans Environment Variables");
    error_log("Voir RENDER_DB_CONFIG.md pour les instructions");
    die($error_msg . " Vérifiez les variables d'environnement DB_* sur Render. Voir RENDER_DB_CONFIG.md");
}

// Initialiser la connexion MySQLi
$conn = mysqli_init();

// Configuration SSL pour Aiven (REQUIRED)
if (!empty($ssl_ca) && file_exists($ssl_ca)) {
    // Utiliser le certificat CA si disponible
    mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);
} else {
    // Pas de certificat, mais SSL requis quand même (Aiven accepte sans certificat)
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
}

// Connexion avec SSL obligatoire pour Aiven
if (!mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    $error_msg = "Erreur de connexion MySQL Aiven : " . mysqli_connect_error() . " (Code: " . mysqli_connect_errno() . ")";
    error_log($error_msg);
    error_log("Host: $host:$port");
    error_log("Database: $database");
    error_log("User: $username");
    error_log("Password: " . ($password ? 'DEFINI' : 'VIDE'));
    die($error_msg);
}

// Définir le charset UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Retourner la connexion
return $conn;

?>
