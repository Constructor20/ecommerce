<?php
session_start();
include 'bdd.php';

if (!isset($_SESSION['username'])) {
    header('Location: connexion.php');
    exit();
}

$username = $_SESSION['username'];
$successMessage = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur introuvable.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newUsername = htmlspecialchars(trim($_POST['username']));
        $newEmail = htmlspecialchars(trim($_POST['email']));
        $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        try {
            $sql = "UPDATE users SET username = :username, email = :email" . ($newPassword ? ", password = :password" : "") . " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $newUsername);
            $stmt->bindParam(":email", $newEmail);
            if ($newPassword) {
                $stmt->bindParam(":password", $newPassword);
            }
            $stmt->bindParam(":id", $user['id']);
            $stmt->execute();

            $_SESSION['username'] = $newUsername;
            $_SESSION['email'] = $newEmail;
            
            $successMessage = "Informations mises à jour avec succès.";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css">

    <style>

        /* Container principal du profil */
.profile-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 30px;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Titre */
.profile-container h2 {
    text-align: center;
    color: #333;
    margin-bottom: 25px;
    font-size: 1.8rem;
}

/* Étiquettes du formulaire */
.profile-container label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
}

/* Champs de saisie */
.profile-container input[type="text"],
.profile-container input[type="email"],
.profile-container input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease;
}

.profile-container input[type="text"]:focus,
.profile-container input[type="email"]:focus,
.profile-container input[type="password"]:focus {
    border-color: #007acc;
    outline: none;
}

/* Bouton de soumission */
.profile-container input[type="submit"] {
    width: 100%;
    background-color: #007acc;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.profile-container input[type="submit"]:hover {
    background-color: #005eaa;
}

/* Lien vers la page d'accueil */
.profile-container + button {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #007acc;
    text-decoration: none;
    font-weight: 500;
}

.profile-container + button:hover {
    text-decoration: underline;
}

.success-message {
    text-align: center;
    color: green;
    margin-top: 10px;
    font-weight: bold;
}


    </style>
</head>
<body>
    <?php include 'navbar.php';?>
    <div class="profile-container">
        <h2>Mon Profil</h2>
        <form action = "compte.php" method="POST">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label for="password">Nouveau mot de passe: </label>
            <input type="password" id="password" name="password">

            <input type="submit" value="Mettre à jour">
        </form>
            <button onclick="window.location.href='index.html'" class="btn btn-secondary" style="margin-top: 10px;">
                Main Page
            </button>
            <?php if (!empty($successMessage)): ?>
                <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
            <?php endif; ?>

    </div>  

    <?php include 'footer.php';?>
</body>
</html>