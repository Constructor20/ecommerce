<?php
session_start();

// Supprime toutes les variables de session
$_SESSION = array();

// Si un cookie de session existe, on le supprime aussi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruit complètement la session
session_destroy();

// Redirige vers la page de connexion (ou accueil)
header("Location: connexion.php");
exit();
