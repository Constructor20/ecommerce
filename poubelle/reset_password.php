<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
    include 'bdd.php';
    $token=htmlspecialchars(trim($_POST["token"]));
    $new_password=trim($_POST["new_password"]);
    $confirm_password=trim($_POST["confirm_password"]);

    if ($new_password !== $confirm_password){
        echo "<p style='color: red;'>Les mots de passe ne correspondent pas</p>";
        exit;
    }
    if (strlen($new_password) < 8){
        echo "<p style='color: red;'>Le mot de passe doit contenir au moins 8 caractères.</p>";
        exit;
    }

    try {
        $conn = new PDO("mysql:host=$servername,dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT email FROM password_reset WHERE token = :token AND expires_at >= NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $email = $stmt->fetchColumn();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $conn->execute([":password" => $hashed_password, ":email" => $email]);
            $conn->prepare("DELETE FROM password_reset WHERE token = :token");
            $conn->execute(['token' => $token]);

            header('location: ../connexion.php');
        } else {
            echo "<p style='color: red;'>Le lien de rénitialisation est invalide ou expiré</p>";
        }
    } catch(PDOException $e){
        echo "Erreur : " . $e->getMessage();
    }
} else if (isset($_GET["token"])){
    $token = htmlspecialchars($_GET["token"]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="login-container">
    <div class="form-continer">
        <h2>Rénitialisation du mot de passe</h2>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value ="<?= htmlspecialchars($token);?>">
            <label for="new_password">Nouveau mot de passe:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirmer le mot de passe:</label>
            <input type="password" id="confirm_password" name="confirm_password" required></input>
            <button type="submit">Rénitialiser</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>