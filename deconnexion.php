<?php
// deconnexion.php — Destruction de la session et redirection
session_start();
session_destroy();
header('Location: accueil.php');
exit();
