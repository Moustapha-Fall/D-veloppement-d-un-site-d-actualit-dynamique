<?php
// categorie.php — Articles filtrés par catégorie
$base_url = '';
require_once 'includes/entete.php';
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: accueil.php'); exit(); }

// Récupérer la catégorie
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$categorie = $stmt->fetch();
if (!$categorie) { header('Location: accueil.php'); exit(); }

$titre_page = 'Catégorie : ' . $categorie['nom'];

// Pagination
$par_page = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE categorie_id = ?');
$stmt->execute([$id]);
$total = (int)$stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total / $par_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $par_page;

$stmt = $pdo->prepare('
    SELECT a.id, a.titre, a.description, a.date_publication, a.image,
           u.prenom, u.nom AS auteur_nom
    FROM articles a
    JOIN utilisateurs u ON a.auteur_id = u.id
    WHERE a.categorie_id = ?
    ORDER BY a.date_publication DESC
    LIMIT ? OFFSET ?
');
$stmt->execute([$id, $par_page, $offset]);
$articles = $stmt->fetchAll();
?>

<?php require_once 'includes/menu.php'; ?>

<div class="page-hero">
    <h1>🏷 <?= htmlspecialchars($categorie['nom']) ?></h1>
    <?php if ($categorie['description']): ?>
        <p><?= htmlspecialchars($categorie['description']) ?></p>
    <?php endif; ?>
</div>

<div class="container">
    <nav class="breadcrumb mt-2">
        <a href="accueil.php">Accueil</a>
        <span class="sep">›</span>
        <span><?= htmlspecialchars($categorie['nom']) ?></span>
    </nav>

    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <h3>Aucun article dans cette catégorie</h3>
            <a href="accueil.php" class="btn btn-outline mt-2">← Retour à l'accueil</a>
        </div>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $art): ?>
                <div class="article-card">
                    <?php if ($art['image'] && file_exists('uploads/' . $art['image'])): ?>
                        <img class="card-img" src="uploads/<?= htmlspecialchars($art['image']) ?>"
                             alt="<?= htmlspecialchars($art['titre']) ?>">
                    <?php else: ?>
                        <div class="card-img-placeholder">📄</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="card-category"><?= htmlspecialchars($categorie['nom']) ?></span>
                        <h2 class="card-title">
                            <a href="article.php?id=<?= (int)$art['id'] ?>">
                                <?= htmlspecialchars($art['titre']) ?>
                            </a>
                        </h2>
                        <p class="card-desc"><?= htmlspecialchars(mb_substr($art['description'], 0, 140)) ?>…</p>
                        <div class="card-meta">
                            <span>✍ <?= htmlspecialchars($art['prenom'] . ' ' . $art['auteur_nom']) ?></span>
                            <span>🕒 <?= date('d/m/Y', strtotime($art['date_publication'])) ?></span>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="card-actions">
                                <a href="articles/modifier.php?id=<?= (int)$art['id'] ?>" class="btn btn-secondary btn-sm">✏ Modifier</a>
                                <a href="articles/supprimer.php?id=<?= (int)$art['id'] ?>" class="btn btn-danger btn-sm confirm-delete">🗑 Supprimer</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?id=<?= $id ?>&page=<?= $page - 1 ?>">&laquo; Précédent</a>
                <?php else: ?>
                    <span class="disabled">&laquo; Précédent</span>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <?php if ($p === $page): ?>
                        <span class="current"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?id=<?= $id ?>&page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?id=<?= $id ?>&page=<?= $page + 1 ?>">Suivant &raquo;</a>
                <?php else: ?>
                    <span class="disabled">Suivant &raquo;</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/pied.php'; ?>
