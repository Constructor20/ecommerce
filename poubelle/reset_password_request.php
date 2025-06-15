<?php 
include 'bdd.php';

date_default_timezone_set('Europe/Paris');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Supprimer les anciens tokens expirés
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE expires_at < NOW() - INTERVAL 1 MINUTE");
        $stmt->execute();

        // Vérifier si l'utilisateur existe
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Insérer le token
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (:email, :token, :expires_at, NOW())");
            $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expiry]);

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/reset_password.php?token=$token";

            $subject = "=?UTF-8?B?" . base64_encode("Rénitialisation de votre mot de passe") . "?=";

            $message = "
            <html>
            <head>
                <title>Rénitialisation de votre mot de passe</title>
            </head>
            <body>
                <p>Bonjour,</p>
                <p>Cliquez sur le lien ci-dessous pour rénitialiser votre mot de passe</p>
                <p><a href='$resetLink' style='color: blue; text-decoration: underline;'>Rénitialiser mon mot de passe</a></p>
                <p>Ce lien expirera dans 15 minutes.</p>
                <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail.</p>
            </body>
            </html>
            ";

            $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";

            if (mail($email, $subject, $message, $headers)) {
                echo "<p style='color: green;'>Un lien de réinitialisation a été envoyé à votre adresse e-mail.</p>";
            } else {
                echo "<p style='color: red;'>Erreur lors de l'envoi de l'e-mail</p>";
            }
        } else {
            echo "<p style='color: red;'>Adresse e-mail non trouvée.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
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
            <form action="reset_password_request.php" method="POST">
                <label for="email">Entrez votre adresse e-mail :</label>
                <input type="email" id="email" name="email" required>
                <button typ="submit">Envoyer</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>