<?php
// articles/supprimer.php — Suppression d'un article et redirection
$base_url = '../';
require_once '../includes/entete.php';
require_once '../config/db.php';

// Protection
if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: ../accueil.php'); exit(); }

// Vérifier que l'article existe
$stmt = $pdo->prepare('SELECT image FROM articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: ../accueil.php');
    exit();
}

// Supprimer l'image associée si elle existe
if ($article['image'] && file_exists('../uploads/' . $article['image'])) {
    unlink('../uploads/' . $article['image']);
}

// Supprimer l'article (PDO prepare obligatoire)
$stmt = $pdo->prepare('DELETE FROM articles WHERE id = ?');
$stmt->execute([$id]);

// Redirection après suppression — pas d'affichage
header('Location: ../accueil.php');
exit();
