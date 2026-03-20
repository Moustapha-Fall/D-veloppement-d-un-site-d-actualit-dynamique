<?php
// utilisateurs/supprimer.php — Suppression d'un compte utilisateur (admin uniquement)
$base_url = '../';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['role'] !== 'admin') { header('Location: ../accueil.php'); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit(); }

// L'admin ne peut pas supprimer son propre compte
if ($id === (int)$_SESSION['user_id']) {
    header('Location: liste.php?msg=erreur_auto');
    exit();
}

// Vérifier que le compte existe
$stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE id = ?');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: liste.php');
    exit();
}

// Suppression
$stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
$stmt->execute([$id]);

header('Location: liste.php?msg=suppr');
exit();
