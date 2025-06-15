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
    }

    .signup-container {
        max-width: 400px;
        margin: 60px auto;
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
</style>

<body>
    <?php include 'navbar.php';?>
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