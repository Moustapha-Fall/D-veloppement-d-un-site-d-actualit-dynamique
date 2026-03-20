<?php
// articles/modifier.php — Formulaire de modification d'un article
$base_url   = '../';
$titre_page = 'Modifier l\'article';
require_once '../includes/entete.php';
require_once '../config/db.php';

// Protection
if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: ../accueil.php'); exit(); }

// Récupérer l'article existant
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();
if (!$article) { header('Location: ../accueil.php'); exit(); }

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
$erreurs = [];

// Pré-remplir avec les valeurs de l'article
$valeurs = [
    'titre'        => $article['titre'],
    'description'  => $article['description'],
    'contenu'      => $article['contenu'],
    'categorie_id' => $article['categorie_id'],
];

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['titre']        = trim($_POST['titre'] ?? '');
    $valeurs['description']  = trim($_POST['description'] ?? '');
    $valeurs['contenu']      = trim($_POST['contenu'] ?? '');
    $valeurs['categorie_id'] = (int)($_POST['categorie_id'] ?? 0);

    // Validation PHP
    if (empty($valeurs['titre'])) $erreurs[] = 'Le titre est obligatoire.';
    elseif (mb_strlen($valeurs['titre']) > 255) $erreurs[] = 'Titre trop long (max 255).';
    if (empty($valeurs['description'])) $erreurs[] = 'La description est obligatoire.';
    if (empty($valeurs['contenu'])) $erreurs[] = 'Le contenu est obligatoire.';
    if ($valeurs['categorie_id'] <= 0) $erreurs[] = 'Sélectionnez une catégorie.';

    // Image (optionnelle)
    $nom_image = $article['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext_ok = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $ext_ok)) {
            $erreurs[] = 'Format image non autorisé.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $erreurs[] = 'Image trop lourde (max 2 Mo).';
        } else {
            // Supprimer l'ancienne image si elle existe
            if ($nom_image && file_exists('../uploads/' . $nom_image)) {
                unlink('../uploads/' . $nom_image);
            }
            $nom_image = uniqid('img_', true) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $nom_image);
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('
            UPDATE articles
            SET titre = ?, description = ?, contenu = ?, categorie_id = ?, image = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $valeurs['titre'],
            $valeurs['description'],
            $valeurs['contenu'],
            $valeurs['categorie_id'],
            $nom_image,
            $id
        ]);
        header('Location: ../article.php?id=' . $id);
        exit();
    }
}
?>

<div class="container-narrow">
    <nav class="breadcrumb mt-2">
        <a href="../accueil.php">Accueil</a>
        <span class="sep">›</span>
        <a href="../article.php?id=<?= $id ?>">Article</a>
        <span class="sep">›</span>
        <span>Modifier</span>
    </nav>

    <div class="form-card mt-2">
        <h2>Modifier l'article</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formArticle" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="titre">Titre <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="titre" name="titre"
                       value="<?= htmlspecialchars($valeurs['titre']) ?>" maxlength="255">
            </div>
            <div class="form-group">
                <label for="description">Résumé <span style="color:var(--clr-danger)">*</span></label>
                <textarea id="description" name="description" rows="3" maxlength="500"><?= htmlspecialchars($valeurs['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="contenu">Contenu complet <span style="color:var(--clr-danger)">*</span></label>
                <textarea id="contenu" name="contenu" rows="12"><?= htmlspecialchars($valeurs['contenu']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="categorie_id">Catégorie <span style="color:var(--clr-danger)">*</span></label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                            <?= $valeurs['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Image</label>
                <?php if ($article['image'] && file_exists('../uploads/' . $article['image'])): ?>
                    <div class="current-image">
                        <img src="../uploads/<?= htmlspecialchars($article['image']) ?>" alt="Image actuelle">
                        <div class="current-image-info">
                            <div class="current-image-label">Image actuelle</div>
                            <div class="current-image-name"><?= htmlspecialchars($article['image']) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="upload-zone">
                    <div class="upload-zone-icon">+</div>
                    <div class="upload-zone-text"><?= $article['image'] ? 'Changer l\'image' : 'Ajouter une image' ?></div>
                    <div class="upload-zone-hint">JPG, PNG, WebP, GIF - Max 2 Mo</div>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
                <div class="upload-preview">
                    <img src="" alt="Apercu">
                    <div class="upload-preview-info">
                        <span class="upload-preview-name"></span>
                        <span class="upload-preview-size"></span>
                        <button type="button" class="upload-preview-remove">Supprimer</button>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-1 flex-wrap">
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <a href="../article.php?id=<?= $id ?>" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
