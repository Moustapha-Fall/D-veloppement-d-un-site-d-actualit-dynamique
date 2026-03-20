<?php
// categories/ajouter.php — Formulaire d'ajout de catégorie
$base_url   = '../';
$titre_page = 'Nouvelle catégorie';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$erreurs = [];
$valeurs = ['nom' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom']         = trim($_POST['nom'] ?? '');
    $valeurs['description'] = trim($_POST['description'] ?? '');

    // Validation PHP
    if (empty($valeurs['nom'])) {
        $erreurs[] = 'Le nom de la catégorie est obligatoire.';
    } elseif (mb_strlen($valeurs['nom']) > 100) {
        $erreurs[] = 'Le nom ne peut pas dépasser 100 caractères.';
    }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (nom, description) VALUES (?, ?)');
            $stmt->execute([$valeurs['nom'], $valeurs['description'] ?: null]);
            header('Location: liste.php?msg=ajout');
            exit();
        } catch (PDOException $e) {
            // Code 23000 = violation de contrainte unique (nom déjà existant)
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Une catégorie avec ce nom existe déjà.';
            } else {
                $erreurs[] = 'Erreur lors de l\'enregistrement.';
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
        <span>Nouvelle catégorie</span>
    </nav>

    <div class="form-card mt-2">
        <h2>🏷 Nouvelle catégorie</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formCategorie" method="POST" novalidate>
            <div class="form-group">
                <label for="nom">Nom <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($valeurs['nom']) ?>"
                       maxlength="100"
                       placeholder="ex: Technologie, Sport, Culture…">
            </div>
            <div class="form-group">
                <label for="description">Description (optionnel)</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Description de la catégorie…"><?= htmlspecialchars($valeurs['description']) ?></textarea>
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <button type="submit" class="btn btn-primary">✅ Créer la catégorie</button>
                <a href="liste.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
