<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: connexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil</title>
    <link rel="icon" type="image/x-icon" href="images/Michael-Myers1.ico">
    <link rel="stylesheet" href="style.css">

    <style>

    .header-welcome-message {
        text-align: center;
        padding: 20px 0;
        font-size: 1.2rem;
        color: #333;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }


    </style>
</head>
<body>
    <?php include 'navbar.php';?>
    <header>
        <div class="header-container">
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h2>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
            </form>
        </div>

        <div class="header-welcome-message">
            <p>Ceci est la page d'accueil de votre application</p>
        </div>
    </header>

    <?php include 'footer.php';?>
</body>
</html>