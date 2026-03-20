<?php
// categories/modifier.php — Formulaire de modification d'une catégorie
$base_url   = '../';
$titre_page = 'Modifier la catégorie';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit(); }

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$categorie = $stmt->fetch();
if (!$categorie) { header('Location: liste.php'); exit(); }

$erreurs = [];
$valeurs = ['nom' => $categorie['nom'], 'description' => $categorie['description'] ?? ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom']         = trim($_POST['nom'] ?? '');
    $valeurs['description'] = trim($_POST['description'] ?? '');

    if (empty($valeurs['nom'])) {
        $erreurs[] = 'Le nom est obligatoire.';
    } elseif (mb_strlen($valeurs['nom']) > 100) {
        $erreurs[] = 'Le nom ne peut pas dépasser 100 caractères.';
    }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare('UPDATE categories SET nom = ?, description = ? WHERE id = ?');
            $stmt->execute([$valeurs['nom'], $valeurs['description'] ?: null, $id]);
            header('Location: liste.php?msg=modif');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Ce nom de catégorie existe déjà.';
            } else {
                $erreurs[] = 'Erreur lors de la mise à jour.';
            }
        }
    }
}
?>

<div class="container-narrow">
    <nav class="breadcrumb mt-2">
        <a href="../accueil.php">Accueil</a>
        <span class="sep">›</span>
        <a href="liste.php">Catégories</a>
        <span class="sep">›</span>
        <span>Modifier</span>
    </nav>

    <div class="form-card mt-2">
        <h2>✏ Modifier la catégorie</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formCategorie" method="POST" novalidate>
            <div class="form-group">
                <label for="nom">Nom <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($valeurs['nom']) ?>"
                       maxlength="100">
            </div>
            <div class="form-group">
                <label for="description">Description (optionnel)</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($valeurs['description']) ?></textarea>
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                <a href="liste.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
