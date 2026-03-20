<?php
// utilisateurs/liste.php — Liste des utilisateurs (admin uniquement)
$base_url   = '../';
$titre_page = 'Gestion des utilisateurs';
require_once '../includes/entete.php';
require_once '../config/db.php';

// Protection ADMIN uniquement
if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['role'] !== 'admin') { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$message = $_GET['msg'] ?? '';

$utilisateurs = $pdo->query('
    SELECT u.*, COUNT(a.id) AS nb_articles
    FROM utilisateurs u
    LEFT JOIN articles a ON a.auteur_id = u.id
    GROUP BY u.id
    ORDER BY u.created_at DESC
')->fetchAll();
?>

<div class="container">
    <nav class="breadcrumb mt-2">
        <a href="../accueil.php">Accueil</a>
        <span class="sep">›</span>
        <span>Utilisateurs</span>
    </nav>

    <?php if ($message === 'ajout'): ?>
        <div class="alert alert-success">✅ Compte créé avec succès.</div>
    <?php elseif ($message === 'modif'): ?>
        <div class="alert alert-success">✅ Compte modifié avec succès.</div>
    <?php elseif ($message === 'suppr'): ?>
        <div class="alert alert-success">✅ Compte supprimé.</div>
    <?php elseif ($message === 'erreur_auto'): ?>
        <div class="alert alert-danger">❌ Vous ne pouvez pas supprimer votre propre compte.</div>
    <?php endif; ?>

    <div class="admin-table-wrap mt-2">
        <div class="admin-table-header">
            <h2>👥 Utilisateurs (<?= count($utilisateurs) ?>)</h2>
            <a href="ajouter.php" class="btn btn-primary">+ Nouveau compte</a>
        </div>

        <?php if (empty($utilisateurs)): ?>
            <div class="empty-state">
                <div class="icon">👤</div>
                <h3>Aucun utilisateur</h3>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th>Login</th>
                        <th>Rôle</th>
                        <th>Articles</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td><?= (int)$u['id'] ?></td>
                            <td>
                                <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                    <span style="font-size:.75rem;color:var(--clr-accent);font-weight:600;">(vous)</span>
                                <?php endif; ?>
                            </td>
                            <td><code><?= htmlspecialchars($u['login']) ?></code></td>
                            <td>
                                <span class="user-badge <?= $u['role'] ?>" style="font-size:.75rem;">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td><?= (int)$u['nb_articles'] ?></td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="modifier.php?id=<?= (int)$u['id'] ?>"
                                       class="btn btn-secondary btn-sm">✏ Modifier</a>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="supprimer.php?id=<?= (int)$u['id'] ?>"
                                           class="btn btn-danger btn-sm confirm-delete"
                                           data-confirm="Supprimer le compte de <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> ?">
                                           🗑 Supprimer</a>
                                    <?php else: ?>
                                        <span class="btn btn-outline btn-sm"
                                              style="opacity:.4;cursor:not-allowed;"
                                              title="Vous ne pouvez pas supprimer votre propre compte">
                                            🔒 Protégé</span>
                                    <?php endif; ?>
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
