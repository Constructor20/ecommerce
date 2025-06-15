<?php
session_start();

include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $video_id = (int) $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $video_id]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$video) {
            die("Video introuvable");
        }
    } else {
        die("ID de vidéo non spécifié");
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($video['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .video-details {
            text-align: center;
            magrin: 20px auto;
        }

        .video-details img {
            max-width: 100%;
            height: auto;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
            
        p {
            margin: 10px 0;
            line-height: 1.6;
        }
    
        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .video-details {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .details-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 600px;
            margin-top: 15px;
        }
        
        .details-left {
            flex: 1;
            text-align: left;
        }
        
        .details-right {
            flex: 0 0 200px;
            text-align: right;
        }

        button[name="add_to_cart"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s;
        }

        buttn[name="add_to_cart"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>    

<div class="container">
    <div class="video-details">
        <h1><?= htmlspecialchars($video['name']); ?></h1>

        <?php
        // Récupérer l'URL vidéo dans une variable dédiée
        $videoUrl = $video['video'];  

        // Affichage conditionnel selon source vidéo
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false):
            parse_str(parse_url($videoUrl, PHP_URL_QUERY), $ytParams);
            $videoId = $ytParams['v'] ?? basename(parse_url($videoUrl, PHP_URL_PATH));
        ?>
            <iframe 
                width="100%" 
                height="200" 
                src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId); ?>" 
                frameborder="0" 
                allowfullscreen>
            </iframe>
        <?php else: ?>
            <video controls>
                <source src="<?= htmlspecialchars($videoUrl); ?>" type="video/mp4">
                Votre navigateur ne supporte pas les vidéos.
            </video>
        <?php endif; ?>

        <p><strong>Description :</strong> <?= htmlspecialchars($video['description'] ?? 'Non disponible'); ?></p>

        <div class="details-container">
            <div class="details-left">
                <p>
                    <strong>Date de mise en ligne:</strong> <?= htmlspecialchars($video['update_date'] ?? 'Non renseignée'); ?><br>
                    <strong>Durée :</strong> <?= htmlspecialchars($video['duration'] ?? 'Non renseignée'); ?>
                </p>
            </div>

            <div class="details-right">
                <form method="post" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($video['id']); ?>">
                    <button type="submit" name="add_to_cart">Ajouter au Panier</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
