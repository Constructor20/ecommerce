<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    include 'bdd.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifie si l'email existe
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                header("Location: index.html");
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Aucun compte trouvé avec cet email.";
        }
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }

    $conn = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #555;
            color: #333;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 40px 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            margin-top: 60px; 
        }

        h2 {
            color: #007acc;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 2rem;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            text-align: left;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #005a99;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px 15px;
            font-size: 1rem;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
            width: 100%;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007acc;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 122, 204, 0.4);
        }

        input[type="submit"] {
            background-color: #007acc;
            color: white;
            border: none;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #005a99;
        }

        .login-container p {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
            text-align: center;
        }

        .login-container a.register-link,
        .login-container a.forgot-password-link {
            color: #007acc;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-container a.register-link:hover,
        .login-container a.forgot-password-link:hover {
            color: #005a99;
            text-decoration: underline;
        }

        p.error-message {
            color: #d93025;
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
        }

        @media (max-width: 450px) {
            .login-container {
                padding: 30px 20px;
            }
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
            object-fit: contain;
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
            width: 200px;
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

        .cart-icon {
            height: 30px;
            width: 30px;
            vertical-align: middle;
        }

    </style>
</head>
<body>
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="navbar">
    <div class="navbar-left">
        <a href="index.php"><img src="images/logo.png" alt="Logo" class="navbar-logo"></a>
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
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php" title="Connexion">Connexion</a>
        <?php else: ?>
            <a href="accueil.php">Accueil</a>
            <a href="compte.php" title="Compte utilisateur">
                <img src="images/user.png" alt="Utilisateur" class="cart-icon">
            </a>
            <a href="cart.php" title="Panier">
                <img src="images/panier.png" alt="Panier" class="cart-icon">
            </a>
            <a href="logout.php" title="Déconnexion">
                <img src="images/exit.png" alt="Déconnexion" class="cart-icon">
            </a>
        <?php endif; ?>
    </div>
</div>


<div class="login-container">
    <h2>Connexion</h2>
    <form action="connexion.php" method="POST" novalidate>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required autofocus>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Se connecter">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p>Pas encore inscrit ? <a href="inscription.php" class="register-link">Inscrivez-vous ici</a></p>
    <p>Mot de passe oublié ? <a href="reset_password_request.php" class="forgot-password-link">Cliquez ici</a></p>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
