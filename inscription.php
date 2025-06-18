<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm-password']));

    if ($password !== $confirmPassword) {
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
        exit;
    }

    include 'bdd.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table si elle n'existe pas
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $conn->exec($sql);

        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<p style='color: red;'>Cet email est déjà utilisé.</p>";
        } else {
            // Insérer un nouvel utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                // Appeler header avant toute sortie
                header("Location: connexion.php");
                exit();
            } else {
                echo "<p style='color: red;'>Une erreur est survenue.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

    // Fermer la connexion à la fin
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Incription</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 80px; /* hauteur approximative de ta navbar */
        padding-bottom: 80px; /* pour ne pas être masqué par le footer */
    }

    .signup-container {
        width: 100%;
        max-width: 400px;
        padding: 30px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .signup-container h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }

    .signup-container form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .signup-container label {
        align-self: flex-start;
        margin: 10px 0 5px;
        font-weight: bold;
        color: #444;
    }

    .signup-container input[type="text"],
    .signup-container input[type="email"],
    .signup-container input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .signup-container input[type="submit"] {
        width: 100%;
        background-color: #007acc;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .signup-container input[type="submit"]:hover {
        background-color: #005eaa;
    }

    #error-msg {
        margin-top: 10px;
        color: red;
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

<body>
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
                <a href="accueil.php" title="Accueil">Accueil</a>
                <a href="connexion.php" title="Connexion">Connexion</a>
            <?php else: ?>
                <a href="accueil.php" title="Accueil">Accueil</a>
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
    <main>
        <div class="signup-container">
            <h2>Inscription</h2>
            <form action="" method="POST" onsubmit="return validateForm()">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            
                <label for="confirm-password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <input  type="submit" value="S'inscrire">
            </form>
            <p id="error-msg"></p>
        </div>
    </main>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value; // Correction ici
            var errorMsg = document.getElementById("error-msg");

            if (password !== confirmPassword) {
                errorMsg.textContent = "Les mots de passe ne correspondent pas.";
                errorMsg.style.color = "red"; // Correction ici
                return false;
            }
            return true;
        }
    </script>
<?php include 'footer.php';?>
</body>
</html> 