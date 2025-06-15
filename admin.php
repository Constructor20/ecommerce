<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    include 'bdd.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND id = 0");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $admin["password"])) {

                $_SESSION["admin"] = $admin["username"];
                echo "<p style='color: green;'>Connexion admin réussie !</p>";
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "<p style='color: red;'>Mot de passe incorrect.</p>";
        } 
        } else {
            echo "<p style='color: red;'>Admin non trouvé ou identifiant incorrect.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f0f2f5;
        color: #333;
    }

    .login-container {
        background-color: #fff;
        padding: 40px 30px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        width: 350px;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #007acc;
        font-weight: 600;
    }

    .form-container label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }

    .form-container input[type="email"],
    .form-container input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1.8px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }

    .form-container input[type="email"]:focus,
    .form-container input[type="password"]:focus {
        border-color: #007acc;
        outline: none;
    }

    .form-container input[type="submit"] {
        width: 100%;
        padding: 12px 15px;
        background-color: #007acc;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-container input[type="submit"]:hover {
        background-color: #005fa3;
    }

    /* Messages d'erreur ou succès */
    p {
        text-align: center;
        margin-bottom: 15px;
        font-weight: 600;
    }

    p[style*="color: red"] {
        color: #d93025;
    }

    p[style*="color: green"] {
        color: #188038;
    }


    </style>
</head>
<body>
    <div class="login-container">
        <div class="form-container">
            <div>
                <h2>Connexion Admin</h2>
                <form action="admin.php" method="POST">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>

                    <input type="submit" value="Se connecter">
                </form>
            </div>
        </div>
    </div>
</body>
</html>