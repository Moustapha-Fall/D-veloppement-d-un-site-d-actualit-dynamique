<?php
// connexion.php — Formulaire de connexion + traitement
$base_url   = '';
$titre_page = 'Connexion';
require_once 'includes/entete.php';
require_once 'config/db.php';

// Si déjà connecté, rediriger vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: accueil.php');
    exit();
}

$erreur = '';
$login_saisi = '';

// Traitement du formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';
    $login_saisi = $login;

    // Validation PHP côté serveur
    if (empty($login) || empty($mdp)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        // Recherche de l'utilisateur par login (requête préparée PDO)
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            // Connexion réussie — régénérer l'ID de session (sécurité)
            session_regenerate_id(true);

            // Stocker les infos en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login']   = $user['login'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['role']    = $user['role'];

            header('Location: accueil.php');
            exit();
        } else {
            $erreur = 'Login ou mot de passe incorrect.';
        }
    }
}
?>

<!-- Pas de menu sur la page connexion — on affiche directement la carte -->
<div class="login-wrapper">
    <div class="login-card">
        <h2>🔐 Connexion</h2>
        <p class="subtitle">Espace réservé aux éditeurs et administrateurs</p>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form id="formConnexion" method="POST" action="connexion.php" novalidate>
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($login_saisi) ?>"
                       placeholder="Votre identifiant"
                       autocomplete="username">
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       placeholder="Votre mot de passe"
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                Se connecter
            </button>
        </form>

        <div class="mt-2 text-center" style="font-size:.85rem;color:var(--clr-text-muted);">
            <a href="accueil.php">← Retour à l'accueil sans connexion</a>
        </div>

        <!-- Aide mémoire pour les tests (à supprimer en production) -->
        <div class="alert alert-info mt-2" style="font-size:.8rem;">
            <strong>Comptes de test :</strong><br>
            Admin : <code>admin</code> / <code>password</code><br>
            Éditeur : <code>editeur</code> / <code>password</code>
        </div>
    </div>
</div>

<?php require_once 'includes/pied.php'; ?>
