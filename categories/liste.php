<?php
// categories/liste.php — Liste des catégories avec actions modifier/supprimer
$base_url   = '../';
$titre_page = 'Gérer les catégories';
require_once '../includes/entete.php';
require_once '../config/db.php';

// Protection : éditeur ou admin
if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['role'], ['editeur', 'admin'])) { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

// Message flash (après redirection)
$message = $_GET['msg'] ?? '';

// Récupérer toutes les catégories avec le nombre d'articles
$categories = $pdo->query('
    SELECT c.*, COUNT(a.id) AS nb_articles
    FROM categories c
    LEFT JOIN articles a ON a.categorie_id = c.id
    GROUP BY c.id
    ORDER BY c.nom
')->fetchAll();
?>

<div class="container">
    <nav class="breadcrumb mt-2">
        <a href="../accueil.php">Accueil</a>
        <span class="sep">›</span>
        <span>Catégories</span>
    </nav>

    <?php if ($message === 'ajout'): ?>
        <div class="alert alert-success">✅ Catégorie ajoutée avec succès.</div>
    <?php elseif ($message === 'modif'): ?>
        <div class="alert alert-success">✅ Catégorie modifiée avec succès.</div>
    <?php elseif ($message === 'suppr'): ?>
        <div class="alert alert-success">✅ Catégorie supprimée.</div>
    <?php elseif ($message === 'erreur_suppr'): ?>
        <div class="alert alert-danger">❌ Impossible de supprimer : des articles utilisent encore cette catégorie.</div>
    <?php endif; ?>

    <div class="admin-table-wrap mt-2">
        <div class="admin-table-header">
            <h2>🏷 Catégories (<?= count($categories) ?>)</h2>
            <a href="ajouter.php" class="btn btn-primary">+ Nouvelle catégorie</a>
        </div>

        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="icon">🏷</div>
                <h3>Aucune catégorie</h3>
                <a href="ajouter.php" class="btn btn-primary mt-2">+ Créer la première catégorie</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= (int)$cat['id'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['nom']) ?></strong></td>
                            <td style="max-width:280px;">
                                <?= $cat['description']
                                    ? htmlspecialchars(mb_substr($cat['description'], 0, 80)) . '…'
                                    : '<span style="color:#999">—</span>' ?>
                            </td>
                            <td>
                                <a href="../accueil.php?categorie=<?= (int)$cat['id'] ?>">
                                    <?= (int)$cat['nb_articles'] ?> article<?= $cat['nb_articles'] != 1 ? 's' : '' ?>
                                </a>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="modifier.php?id=<?= (int)$cat['id'] ?>"
                                       class="btn btn-secondary btn-sm">✏ Modifier</a>
                                    <a href="supprimer.php?id=<?= (int)$cat['id'] ?>"
                                       class="btn btn-danger btn-sm confirm-delete"
                                       data-confirm="Supprimer la catégorie « <?= htmlspecialchars($cat['nom']) ?> » ? (impossible si des articles y sont associés)">
                                       🗑 Supprimer</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
