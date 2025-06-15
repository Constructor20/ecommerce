<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>  

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            margin-top: 80px;
            background-color: #f8f8f8;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 10px 20px;
            z-index: 1000;
        }

        .navbar-logo {
            height: 50px;
            width: 50px;
            object-fit: contain
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            transition: background-color 0.3s ease;
        }

        .navbar-right a:hover {
            background-color: #444;
            border-radius: 5px;
        }

        .cart-link {
            display: inline-block;
        }

        .cart-icon {
            height: 30px;
            width: 30px;
            vertical-align: middle;
        }

        .cart-link:hover .cart-icon {
            filter: brightness(0.8);
        }

        .search-bar form {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .search-bar input[type="text"] {
            border: none;
            outline: none;
            padding: 8px;
            border-radius: 20px;
            font-size: 14px;
        }

        .search-bar button {
            background: none;
            border: none;
            margin-left: 5px;
            cursor: pointer;
        }

        .search-icon {
            height: 20px;
            width: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="navbar-left">
            <img src="images/logo.png" alt="Logo" class="navbar-logo">
        </div>

        <div class="search-bar">
            <form action="search.php" method="get" style="display: flex; align-items: center;">
                <input type="text" name="query" placeholder="Rechercher une vidéo ..." required>
                <button type="submit" style="background: none; border: none; padding: 0; margin-left: 5px;">
                    <img src="images/search.png" alt="Rechercher" class="search-icon">
                </button>
            </form>
        </div>
        
        <div class="navbar-right">
            <?php if (!isset($_SESSION['username'])): ?>
                <a href="connexion.php" class="cart-link">
                <?php else: ?>
                    <a href="accueil.php">Accueil</a>
                    <a href="index2.php">Produit du site</a>
                    <a href="compte.php" class="cart-link">
                        <img src="images/user.png" alt="Utilisateur" class="cart-icon" tittle="Utilisateur">
                    </a>
                    <a href="inscription.php" class="cart-link">
                        <img src="images/inscription.png" alt="Inscription" class="cart-icon" tittle="Inscription">
                    </a>
                    <a href="cart.php" class="cart-link">
                        <img src="images/panier.png" alt="Panier" class="cart-icon" tittle="Panier">
                    </a>
                    <a href="logout.php" class="cart-link">
                        <img src="images/exit.png" alt="Deconnexion" class="cart-icon" tittle="Déconnexion">
                    </a>
                <?php endif; ?>
        </div>
    </div>

</body>
</html>