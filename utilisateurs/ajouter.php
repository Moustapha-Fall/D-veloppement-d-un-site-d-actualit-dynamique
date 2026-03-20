<?php
// utilisateurs/ajouter.php — Création d'un compte utilisateur (admin uniquement)
$base_url   = '../';
$titre_page = 'Nouveau compte';
require_once '../includes/entete.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['role'] !== 'admin') { header('Location: ../accueil.php'); exit(); }

require_once '../includes/menu.php';

$erreurs = [];
$valeurs = ['nom' => '', 'prenom' => '', 'login' => '', 'role' => 'editeur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom']    = trim($_POST['nom'] ?? '');
    $valeurs['prenom'] = trim($_POST['prenom'] ?? '');
    $valeurs['login']  = trim($_POST['login'] ?? '');
    $valeurs['role']   = $_POST['role'] ?? '';
    $mdp               = $_POST['mot_de_passe'] ?? '';

    // Validation PHP
    if (empty($valeurs['nom']))    $erreurs[] = 'Le nom est obligatoire.';
    if (empty($valeurs['prenom'])) $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($valeurs['login']))  $erreurs[] = 'Le login est obligatoire.';
    elseif (mb_strlen($valeurs['login']) < 3) $erreurs[] = 'Login trop court (min 3 caractères).';
    if (empty($mdp))               $erreurs[] = 'Le mot de passe est obligatoire.';
    elseif (mb_strlen($mdp) < 6)   $erreurs[] = 'Mot de passe trop court (min 6 caractères).';
    if (!in_array($valeurs['role'], ['editeur', 'admin'])) $erreurs[] = 'Rôle invalide.';

    if (empty($erreurs)) {
        try {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('
                INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $valeurs['nom'],
                $valeurs['prenom'],
                $valeurs['login'],
                $hash,
                $valeurs['role']
            ]);
            header('Location: liste.php?msg=ajout');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Ce login est déjà utilisé par un autre compte.';
            } else {
                $erreurs[] = 'Erreur lors de la création du compte.';
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
        <span>Nouveau compte</span>
    </nav>

    <div class="form-card mt-2">
        <h2>👤 Nouveau compte utilisateur</h2>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form id="formUser" method="POST" novalidate>
            <input type="hidden" name="_mode" value="creer">

            <div class="d-flex gap-2 flex-wrap">
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label for="prenom">Prénom <span style="color:var(--clr-danger)">*</span></label>
                    <input type="text" id="prenom" name="prenom"
                           value="<?= htmlspecialchars($valeurs['prenom']) ?>"
                           maxlength="100" placeholder="Prénom">
                </div>
                <div class="form-group" style="flex:1;min-width:180px;">
                    <label for="nom">Nom <span style="color:var(--clr-danger)">*</span></label>
                    <input type="text" id="nom" name="nom"
                           value="<?= htmlspecialchars($valeurs['nom']) ?>"
                           maxlength="100" placeholder="Nom de famille">
                </div>
            </div>

            <div class="form-group">
                <label for="login">Login <span style="color:var(--clr-danger)">*</span></label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($valeurs['login']) ?>"
                       maxlength="50" placeholder="Identifiant unique"
                       autocomplete="off">
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe <span style="color:var(--clr-danger)">*</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       placeholder="Minimum 6 caractères"
                       autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="role">Rôle <span style="color:var(--clr-danger)">*</span></label>
                <select id="role" name="role">
                    <option value="editeur" <?= $valeurs['role'] === 'editeur' ? 'selected' : '' ?>>Éditeur</option>
                    <option value="admin"   <?= $valeurs['role'] === 'admin'   ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <small class="text-muted">L'éditeur gère articles et catégories. L'admin gère également les comptes.</small>
            </div>

            <div class="d-flex gap-1 flex-wrap">
                <button type="submit" class="btn btn-primary">✅ Créer le compte</button>
                <a href="liste.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/pied.php'; ?>
