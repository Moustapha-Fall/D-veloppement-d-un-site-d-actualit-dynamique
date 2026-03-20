<?php
// config/db.exemple.php — Version fictive pour GitHub
// Copiez ce fichier en db.php et remplissez vos vraies valeurs

$host     = 'localhost';
$dbname   = 'actualite_db';      // Nom de votre base de données
$username = 'root';              // Votre utilisateur MySQL
$password = 'VOTRE_MOT_DE_PASSE'; // Votre mot de passe MySQL
$charset  = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('<div style="padding:2rem;color:#c00;">Erreur BD : ' . htmlspecialchars($e->getMessage()) . '</div>');
}
