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
        .welcome {
            padding: 20px;
            text-align: center;
        }
        .video-section {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .video-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2Px 5px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 15px;
            width: 300px;
            text-align: center;
        }
        .video-card h3 {
            margin: 0 0 10px;
        }
        .video-card p {
            margin: 10px 0;
        }
        .video-card video {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .cta {
            text-align: center;
            margin: 20px 0;
        }
        .cta a {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .cta a:hover {
            background-color: #0056B3;
        }

    </style>
</head>
<body>
        <?php include 'navbar.php';?>
        <div class="welcome">
            <br>
            <h2>Découvrez Nos Vidéos</h2>
            <p>Nous proposons une large game de vidéos pour vous aider à acquérir les compétences nécessaires pour coder.</p>
        </div>
        <div class="video-section">
            <?php 
            $videos = [
                    ['title' => 'Introduction à HTML', 'desription' => 'Apprenez les bases du HTML et créez vos premières pages web', 'video' => 'video/admi_corriger.mkv'],
                ['title' => 'CSS pour les Débutants', 'desription' => 'Découvrez comment styliser vos pages web avec CSS.', 'video' => 'video/admi_corriger.mkv'],
                ['title' => 'Créer un Site E-commerce', 'desription' => 'Suiviez nos étapes pour construire un site E-commerce à partir de zéro.', 'video' => 'video/admi_corriger.mkv'],
                ['title' => 'Introduction à JavaScript', 'desription' => "Apprenez à ajouter de l'intéractivité à vos sites web avec JavaScript.", 'video' => 'video/admi_corriger.mkv'],
            ];

            foreach ($videos as $video) {
                echo '<div class="video-card">';
                echo '<h3>s' . htmlspecialchars($video['title']) . '</h3>';
                echo '<video controls class="video-player">';
                echo '<source src="' . htmlspecialchars($video['video']) . '" type="video/mp4">';
                echo 'Votre navigateur ne supporte pas les vidéos.';
                echo '</video>';
                echo '<p>' . htmlspecialchars($video['description']) . '</p>';
                echo '<a href="video.php?title=' . urlencode($video['title']) . '">Voir la vidéo</a>';
                echo '</div>';
            }
            ?>

        </div>

    <script>
        document.querySelectorAll('.video-player').forEach(video => {
            video.addEventListener('timeupdate', function() {
                if (this.currentTime > 60) {
                    this.pause();
                    this.currentTime = 0;
                    alert("Vous avez dépassé la limite de visionnage de 1 minute.");
                }
            });
        });
    </script>
    <?php include 'footer.php';?>
</body> 
</html>