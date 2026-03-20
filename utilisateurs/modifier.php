<?php
// utilisateurs/modifier.php — Modification d'un compte (admin uniquement)
$base_url   = '../';
$titre_page = 'Modifier le compte';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['role'] !== 'admin') { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit(); }

$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { header('Location: liste.php'); exit(); }

$erreurs = [];
$valeurs = [
    'nom'    => $user['nom'],
    'prenom' => $user['prenom'],
    'login'  => $user['login'],
    'role'   => $user['role'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom']    = trim($_POST['nom'] ?? '');
    $valeurs['prenom'] = trim($_POST['prenom'] ?? '');
    $valeurs['login']  = trim($_POST['login'] ?? '');
    $valeurs['role']   = $_POST['role'] ?? '';
    $nouveau_mdp       = $_POST['mot_de_passe'] ?? '';

    // Validation PHP
    if (empty($valeurs['nom']))    $erreurs[] = 'Le nom est obligatoire.';
    if (empty($valeurs['prenom'])) $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($valeurs['login']))  $erreurs[] = 'Le login est obligatoire.';
    if (!in_array($valeurs['role'], ['editeur', 'admin'])) $erreurs[] = 'Rôle invalide.';
    if (!empty($nouveau_mdp) && mb_strlen($nouveau_mdp) < 6) {
        $erreurs[] = 'Nouveau mot de passe trop court (min 6 caractères).';
    }

    if (empty($erreurs)) {
        try {
            if (!empty($nouveau_mdp)) {
                // Changer aussi le mot de passe
                $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('
                    UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=?, mot_de_passe=?
                    WHERE id=?
                ');
                $stmt->execute([$valeurs['nom'], $valeurs['prenom'], $valeurs['login'], $valeurs['role'], $hash, $id]);
            } else {
                // Ne pas changer le mot de passe
                $stmt = $pdo->prepare('
                    UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=?
                    WHERE id=?
                ');
                $stmt->execute([$valeurs['nom'], $valeurs['prenom'], $valeurs['login'], $valeurs['role'], $id]);
            }
            header('Location: liste.php?msg=modif');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Ce login est déjà utilisé.';
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
        <a href="liste.php">Utilisateurs</a>
        <span class="sep">›</span>
        <span>Modifier</span>
    </nav>

    <div class="form-card mt-2">
        <h2>✏ Modifier le compte</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formUser" method="POST" novalidate>
            <input type="hidden" name="_mode" value="modifier">

            <div class="d-flex gap-2 flex-wrap">
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label for="prenom">Prénom <span style="color:var(--clr-danger)">*</span></label>
                    <input type="text" id="prenom" name="prenom"
                           value="<?= htmlspecialchars($valeurs['prenom']) ?>" maxlength="100">
                </div>
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label for="nom">Nom <span style="color:var(--clr-danger)">*</span></label>
                    <input type="text" id="nom" name="nom"
                           value="<?= htmlspecialchars($valeurs['nom']) ?>" maxlength="100">
                </div>
            </div>

            <div class="form-group">
                <label for="login">Login <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($valeurs['login']) ?>" maxlength="50">
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       placeholder="Laisser vide pour conserver l'actuel"
                       autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="role">Rôle <span style="color:var(--clr-danger)">*</span></label>
                <select id="role" name="role">
                    <option value="editeur" <?= $valeurs['role'] === 'editeur' ? 'selected' : '' ?>>Éditeur</option>
                    <option value="admin"   <?= $valeurs['role'] === 'admin'   ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>

            <div class="d-flex gap-1 flex-wrap">
                <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                <a href="liste.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
