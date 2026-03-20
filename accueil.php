<?php
// accueil.php — Page d'accueil publique avec liste des articles et pagination
$base_url  = '';
$titre_page = 'Accueil';
require_once 'includes/entete.php';
require_once 'config/db.php';
require_once 'includes/menu.php';

// --- Paramètres de pagination ---
$par_page = 6;
$page_courante = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page_courante - 1) * $par_page;

// Filtre par catégorie (optionnel)
$filtre_cat = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

// --- Comptage total des articles ---
if ($filtre_cat > 0) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE categorie_id = ?');
    $stmt->execute([$filtre_cat]);
} else {
    $stmt = $pdo->query('SELECT COUNT(*) FROM articles');
}
$total_articles = (int)$stmt->fetchColumn();
$total_pages    = max(1, (int)ceil($total_articles / $par_page));
$page_courante  = min($page_courante, $total_pages);

// --- Récupération des articles avec JOIN ---
if ($filtre_cat > 0) {
    $stmt = $pdo->prepare('
        SELECT a.id, a.titre, a.description, a.date_publication, a.image,
               c.nom AS categorie_nom, c.id AS categorie_id,
               u.prenom, u.nom AS auteur_nom
        FROM articles a
        JOIN categories c ON a.categorie_id = c.id
        JOIN utilisateurs u ON a.auteur_id = u.id
        WHERE a.categorie_id = ?
        ORDER BY a.date_publication DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->execute([$filtre_cat, $par_page, $offset]);
} else {
    $stmt = $pdo->prepare('
        SELECT a.id, a.titre, a.description, a.date_publication, a.image,
               c.nom AS categorie_nom, c.id AS categorie_id,
               u.prenom, u.nom AS auteur_nom
        FROM articles a
        JOIN categories c ON a.categorie_id = c.id
        JOIN utilisateurs u ON a.auteur_id = u.id
        ORDER BY a.date_publication DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->execute([$par_page, $offset]);
}
$articles = $stmt->fetchAll();

// --- Récupération des catégories pour le filtre ---
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();

// Nom de la catégorie filtrée (pour affichage)
$nom_filtre = '';
if ($filtre_cat > 0) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $filtre_cat) { $nom_filtre = $cat['nom']; break; }
    }
}
?>

<div class="page-hero">
    <h1>ActuSenegal</h1>
    <p>L'actualite en temps reel - Technologie, Sport, Politique, Education, Culture</p>
</div>

<div class="container">

    <!-- Filtre catégories -->
    <div class="category-filter mt-3">
        <a href="accueil.php" class="<?= $filtre_cat === 0 ? 'active' : '' ?>">Toutes</a>
        <?php foreach ($categories as $cat): ?>
            <a href="accueil.php?categorie=<?= $cat['id'] ?>"
               class="<?= $filtre_cat == $cat['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['nom']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($nom_filtre): ?>
        <div class="alert alert-info">
            Filtrage par catégorie : <strong><?= htmlspecialchars($nom_filtre) ?></strong>
            — <a href="accueil.php">Voir tous les articles</a>
        </div>
    <?php endif; ?>

    <!-- Grille articles -->
    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <div class="icon">--</div>
            <h3>Aucun article disponible</h3>
            <p>Il n'y a pas encore d'articles dans cette categorie.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="articles/ajouter.php" class="btn btn-primary mt-2">+ Creer le premier article</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $art): ?>
                <div class="article-card">
                    <?php if ($art['image'] && file_exists('uploads/' . $art['image'])): ?>
                        <img class="card-img"
                             src="uploads/<?= htmlspecialchars($art['image']) ?>"
                             alt="<?= htmlspecialchars($art['titre']) ?>">
                    <?php else: ?>
                        <div class="card-img-placeholder">A</div>
                    <?php endif; ?>

                    <div class="card-body">
                        <a href="categorie.php?id=<?= (int)$art['categorie_id'] ?>"
                           class="card-category">
                            <?= htmlspecialchars($art['categorie_nom']) ?>
                        </a>
                        <h2 class="card-title">
                            <a href="article.php?id=<?= (int)$art['id'] ?>">
                                <?= htmlspecialchars($art['titre']) ?>
                            </a>
                        </h2>
                        <p class="card-desc"><?= htmlspecialchars(mb_substr($art['description'], 0, 140)) ?>…</p>

                        <div class="card-meta">
                            <span><?= htmlspecialchars($art['prenom'] . ' ' . $art['auteur_nom']) ?></span>
                            <span><?= date('d/m/Y', strtotime($art['date_publication'])) ?></span>
                        </div>

                        <!-- Boutons admin/editeur -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="card-actions">
                                <a href="articles/modifier.php?id=<?= (int)$art['id'] ?>"
                                   class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="articles/supprimer.php?id=<?= (int)$art['id'] ?>"
                                   class="btn btn-danger btn-sm confirm-delete"
                                   data-confirm="Supprimer l'article <?= htmlspecialchars($art['titre']) ?> ?">
                                   Supprimer</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php
                $qs = $filtre_cat ? "&categorie=$filtre_cat" : '';
                ?>
                <!-- Bouton Précédent -->
                <?php if ($page_courante > 1): ?>
                    <a href="?page=<?= $page_courante - 1 . $qs ?>">&laquo; Précédent</a>
                <?php else: ?>
                    <span class="disabled">&laquo; Précédent</span>
                <?php endif; ?>

                <!-- Numéros de pages -->
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <?php if ($p === $page_courante): ?>
                        <span class="current"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p . $qs ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Bouton Suivant -->
                <?php if ($page_courante < $total_pages): ?>
                    <a href="?page=<?= $page_courante + 1 . $qs ?>">Suivant &raquo;</a>
                <?php else: ?>
                    <span class="disabled">Suivant &raquo;</span>
                <?php endif; ?>
            </div>
            <p class="text-center text-muted" style="font-size:.85rem;margin-bottom:1rem;">
                Page <?= $page_courante ?> sur <?= $total_pages ?>
                (<?= $total_articles ?> article<?= $total_articles > 1 ? 's' : '' ?>)
            </p>
        <?php endif; ?>
    <?php endif; ?>

</div><!-- /.container -->

<?php require_once 'includes/pied.php'; ?>
