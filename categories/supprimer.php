<?php
// categories/supprimer.php — Suppression d'une catégorie
$base_url = '../';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit(); }

// Vérifier que la catégorie existe
$stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
$stmt->execute([$id]);
if (!$stmt->fetch()) { header('Location: liste.php'); exit(); }

// Vérifier qu'aucun article n'utilise cette catégorie
$stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE categorie_id = ?');
$stmt->execute([$id]);
$nb_articles = (int)$stmt->fetchColumn();

if ($nb_articles > 0) {
    // Impossible de supprimer — des articles y sont liés
    header('Location: liste.php?msg=erreur_suppr');
    exit();
}

// Suppression sécurisée
$stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
$stmt->execute([$id]);

header('Location: liste.php?msg=suppr');
exit();
