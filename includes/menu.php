<?php
// includes/menu.php — Navigation dynamique selon le rôle
// session_start() doit avoir été appelé dans entete.php avant ce fichier
$base = $base_url ?? '';
?>
<header class="site-header">
    <div class="header-inner">
        <a href="<?= $base ?>accueil.php" class="site-logo">
            <span class="logo-icon">📰</span>
            <span class="logo-text">ActuSénégal</span>
        </a>

        <nav class="main-nav">
            <a href="<?= $base ?>accueil.php">Accueil</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Liens réservés aux éditeurs et admins -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle">Articles ▾</a>
                    <div class="nav-dropdown-menu">
                        <a href="<?= $base ?>articles/ajouter.php">+ Nouvel article</a>
                    </div>
                </div>

                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle">Catégories ▾</a>
                    <div class="nav-dropdown-menu">
                        <a href="<?= $base ?>categories/liste.php">Gérer les catégories</a>
                        <a href="<?= $base ?>categories/ajouter.php">+ Nouvelle catégorie</a>
                    </div>
                </div>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                <!-- Liens réservés à l'admin uniquement -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle">Utilisateurs ▾</a>
                    <div class="nav-dropdown-menu">
                        <a href="<?= $base ?>utilisateurs/liste.php">Gérer les comptes</a>
                        <a href="<?= $base ?>utilisateurs/ajouter.php">+ Nouveau compte</a>
                    </div>
                </div>
                <?php endif; ?>

                <div class="nav-user">
                    <span class="user-badge <?= $_SESSION['role'] ?>">
                        <?= htmlspecialchars($_SESSION['prenom']) ?>
                        <small>(<?= htmlspecialchars($_SESSION['role']) ?>)</small>
                    </span>
                    <a href="<?= $base ?>deconnexion.php" class="btn-deconnexion">Déconnexion</a>
                </div>

            <?php else: ?>
                <!-- Visiteur non connecté -->
                <a href="<?= $base ?>connexion.php" class="btn-connexion">Connexion</a>
            <?php endif; ?>
        </nav>

        <!-- Menu burger mobile -->
        <button class="menu-burger" id="menuBurger" aria-label="Ouvrir le menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main class="site-main">
