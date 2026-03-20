<?php
// articles/ajouter.php — Formulaire de création d'un article
$base_url   = '../';
$titre_page = 'Nouvel article';
require_once '../includes/entete.php';
require_once '../config/db.php';

// --- Protection : éditeur ou admin requis ---
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit();
}
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) {
    header('Location: ../accueil.php');
    exit();
}

require_once '../includes/menu.php';

$erreurs  = [];
$success  = '';
$valeurs  = ['titre' => '', 'description' => '', 'contenu' => '', 'categorie_id' => ''];

// Catégories pour le select
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['titre']        = trim($_POST['titre'] ?? '');
    $valeurs['description']  = trim($_POST['description'] ?? '');
    $valeurs['contenu']      = trim($_POST['contenu'] ?? '');
    $valeurs['categorie_id'] = (int)($_POST['categorie_id'] ?? 0);

    // Validation PHP
    if (empty($valeurs['titre'])) {
        $erreurs[] = 'Le titre est obligatoire.';
    } elseif (mb_strlen($valeurs['titre']) > 255) {
        $erreurs[] = 'Le titre ne peut pas dépasser 255 caractères.';
    }
    if (empty($valeurs['description'])) {
        $erreurs[] = 'La description courte est obligatoire.';
    } elseif (mb_strlen($valeurs['description']) > 500) {
        $erreurs[] = 'La description ne peut pas dépasser 500 caractères.';
    }
    if (empty($valeurs['contenu'])) {
        $erreurs[] = 'Le contenu est obligatoire.';
    }
    if ($valeurs['categorie_id'] <= 0) {
        $erreurs[] = 'Veuillez sélectionner une catégorie.';
    }

    // Gestion de l'image (bonus)
    $nom_image = null;
    if (!empty($_FILES['image']['name'])) {
        $ext_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $ext_autorisees)) {
            $erreurs[] = 'Format d\'image non autorisé (jpg, png, webp, gif).';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $erreurs[] = 'L\'image ne doit pas dépasser 2 Mo.';
        } else {
            $nom_image = uniqid('img_', true) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $nom_image);
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare('
            INSERT INTO articles (titre, description, contenu, categorie_id, auteur_id, image)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $valeurs['titre'],
            $valeurs['description'],
            $valeurs['contenu'],
            $valeurs['categorie_id'],
            $_SESSION['user_id'],
            $nom_image
        ]);
        header('Location: ../accueil.php');
        exit();
    }
}
?>

<div class="container-narrow">
    <nav class="breadcrumb mt-2">
        <a href="../accueil.php">Accueil</a>
        <span class="sep">›</span>
        <span>Nouvel article</span>
    </nav>

    <div class="form-card mt-2">
        <h2>Nouvel article</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formArticle" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="titre">Titre <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="titre" name="titre"
                       value="<?= htmlspecialchars($valeurs['titre']) ?>"
                       maxlength="255" placeholder="Titre de l'article">
            </div>
            <div class="form-group">
                <label for="description">Résumé (description courte) <span style="color:var(--clr-danger)">*</span></label>
                <textarea id="description" name="description" rows="3"
                          maxlength="500"
                          placeholder="Résumé affiché sur la page d'accueil..."><?= htmlspecialchars($valeurs['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="contenu">Contenu complet <span style="color:var(--clr-danger)">*</span></label>
                <textarea id="contenu" name="contenu" rows="10"
                          placeholder="Contenu intégral de l'article..."><?= htmlspecialchars($valeurs['contenu']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="categorie_id">Catégorie <span style="color:var(--clr-danger)">*</span></label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                            <?= $valeurs['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Image (optionnel)</label>
                <div class="upload-zone">
                    <div class="upload-zone-icon">+</div>
                    <div class="upload-zone-text">Cliquez ou glissez une image ici</div>
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
                <button type="submit" class="btn btn-primary">Publier</button>
                <a href="../accueil.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
