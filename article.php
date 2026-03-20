<?php
// article.php — Affichage du contenu complet d'un article
$base_url = '';
require_once 'includes/entete.php';
require_once 'config/db.php';

// Forcer l'ID en entier (protection injection SQL)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: accueil.php');
    exit();
}

// Récupérer l'article avec jointures
$stmt = $pdo->prepare('
    SELECT a.*, c.nom AS categorie_nom, c.id AS categorie_id,
           u.prenom, u.nom AS auteur_nom
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.auteur_id = u.id
    WHERE a.id = ?
');
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: accueil.php');
    exit();
}

$titre_page = $article['titre'];
?>

<?php require_once 'includes/menu.php'; ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mt-2">
        <a href="accueil.php">Accueil</a>
        <span class="sep">›</span>
        <a href="categorie.php?id=<?= (int)$article['categorie_id'] ?>">
            <?= htmlspecialchars($article['categorie_nom']) ?>
        </a>
        <span class="sep">›</span>
        <span><?= htmlspecialchars(mb_substr($article['titre'], 0, 50)) ?>…</span>
    </nav>

    <article class="article-detail">
        <!-- Image -->
        <?php if ($article['image'] && file_exists('uploads/' . $article['image'])): ?>
            <img class="article-img"
                 src="uploads/<?= htmlspecialchars($article['image']) ?>"
                 alt="<?= htmlspecialchars($article['titre']) ?>">
        <?php endif; ?>

        <!-- Catégorie -->
        <a href="categorie.php?id=<?= (int)$article['categorie_id'] ?>" class="card-category">
            <?= htmlspecialchars($article['categorie_nom']) ?>
        </a>

        <!-- Titre -->
        <h1><?= htmlspecialchars($article['titre']) ?></h1>

        <!-- Méta -->
        <div class="article-meta">
            <span>✍ <?= htmlspecialchars($article['prenom'] . ' ' . $article['auteur_nom']) ?></span>
            <span>🕒 <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?></span>
        </div>

        <!-- Résumé -->
        <p style="font-style:italic;color:var(--clr-text-muted);margin-bottom:1.5rem;font-size:1.05rem;">
            <?= htmlspecialchars($article['description']) ?>
        </p>

        <hr style="border:none;border-top:2px solid var(--clr-border);margin-bottom:1.5rem;">

        <!-- Contenu complet -->
        <div class="article-body">
            <?php
            // Affichage sécurisé du contenu : on échappe puis on convertit les sauts de ligne
            $contenu_echappe = htmlspecialchars($article['contenu']);
            $paragraphes = explode("\n\n", $contenu_echappe);
            foreach ($paragraphes as $para) {
                $para = trim($para);
                if ($para !== '') {
                    echo '<p>' . nl2br($para) . '</p>';
                }
            }
            ?>
        </div>

        <!-- Boutons éditeur -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="d-flex gap-1 mt-3" style="border-top:1px solid var(--clr-border);padding-top:1rem;">
                <a href="articles/modifier.php?id=<?= (int)$article['id'] ?>"
                   class="btn btn-secondary">✏ Modifier cet article</a>
                <a href="articles/supprimer.php?id=<?= (int)$article['id'] ?>"
                   class="btn btn-danger confirm-delete"
                   data-confirm="Supprimer cet article ?">🗑 Supprimer</a>
                <a href="accueil.php" class="btn btn-outline">← Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <div class="mt-3">
                <a href="accueil.php" class="btn btn-outline">← Retour à l'accueil</a>
            </div>
        <?php endif; ?>
    </article>
</div>

<?php require_once 'includes/pied.php'; ?>
