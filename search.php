<?php
include 'bdd.php';

if (isset($_GET['query'])) {
    $search = htmlspecialchars($_GET['query']);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$dbname", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :query");

    $stmt->execute(['query' => '%' . $search . '%']);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">

    <style>

        .catalogue-module {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    padding-top: 100px;
    font-family: Arial, sans-serif;
}

.catalogue-module form#filterForm {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.catalogue-module form#filterForm label {
    display: block;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}

.catalogue-module form#filterForm input[type="checkbox"] {
    margin-right: 5px;
    cursor: pointer;
}

.catalogue-module .price-slider {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.catalogue-module .price-slider input[type="range"] {
    width: 45%;
    -webkit-appearance: none;
    height: 6px;
    background: #007acc;
    border-radius: 5px;
    outline: none;
    cursor: pointer;
}

.catalogue-module .price-values {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #555;
    padding: 0 10px;
}

.catalogue-module .product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    justify-content: center;
}


.catalogue-module .product-item {
    background-color: #fff;
    border-radius: 10px;
    width: 280px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.2s;
}

.catalogue-module .product-item:hover {
    transform: translateY(-5px);
}

.catalogue-module .product-item img {
    width: 100%;
    height: auto;
    border-radius: 6px;
    object-fit: cover;
}

.catalogue-module .product-item iframe {
    width: 100%;
    height: 160px;
    border: none;
    border-radius: 6px;
    margin-top: 10px;
    background-color: #f0f0f0;
}

.catalogue-module .product-item h2 {
    margin: 10px 0;
    font-size: 1.1rem;
}

.catalogue-module .product-item h2 a {
    color: #333;
    text-decoration: none;
}

.catalogue-module .product-item h2 a:hover {
    color: #d00;
}

.catalogue-module .product-item p {
    margin: 10px 0;
    font-weight: bold;
}

.catalogue-module .product-item form {
    margin-top: 10px;
}

.catalogue-module .product-item button {
    background-color: #007acc;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background-color 0.2s ease-in-out;
}

.catalogue-module .product-item button:hover {
    background-color: #005b99;
}

.catalogue-module a[href="cart.php"] {
    display: block;
    margin-top: 30px;
    text-align: center;
    font-weight: bold;
    color: #007acc;
    text-decoration: none;
}

.catalogue-module a[href="cart.php"]:hover {
    text-decoration: underline;
}

/* Responsive */
@media screen and (max-width: 768px) {
    .catalogue-module .price-slider {
        flex-direction: column;
        gap: 10px;
    }

    .catalogue-module .price-slider input[type="range"] {
        width: 100%;
    }

    .catalogue-module .product-item {
        width: 90%;
    }
}

.catalogue-module .product-item video {
    width: 100%;
    height: auto;
    border-radius: 6px;
    margin-top: 10px;
    background-color: #000;
}

#filterForm {
    margin-top: 40px;
    padding: 20px;
    border-top: 1px solid #ccc;
    background-color: #f9f9f9;
}

    .catalogue-module .product-item iframe.youtube-embed {
    width: 100%;
    height: 200px;
    border: none;
    border-radius: 6px;
    margin-top: 10px;
    background-color: #f0f0f0;
}


    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<br><br><br><br>
    <div class="catalogue-module">
        <h1 style="text-align: center; margin-top: 20px;">Résultats pour "<?= htmlspecialchars($search) ?>"</h1>
        <div class="product-list">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $product): ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?php htmlspecialchars($product['name']) ?>">
                        <?php
                        $video = $product['video'];
                        if (strpos($video, 'youtube.com') !== false || strpos($video, 'youtu.be') !== false):
                            parse_str(parse_url($video, PHP_URL_QUERY), $ytParams);
                            $videoId = $ytParams['v'] ?? basename(parse_url($video, PHP_URL_PATH));
                        ?>
                            <iframe 
                                width="100%" 
                                height="200" 
                                src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>" 
                                frameborder="0" 
                                allowfullscreen 
                                class="youtube-embed">
                            </iframe>
                        <?php else: ?>
                            <video controls>
                                <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
                                Votre navigateur ne supporte pas les vidéos.
                            </video>
                        <?php endif; ?>

                        <h2>
                            <a href="video.php?id=<?= htmlspecialchars($product['id']) ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h2>
                        <p>Prix: €<?= htmlspecialchars($product['price']) ?></p>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                            <button type="submit" name="add_to_cart">Ajouter au Panier</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun résultat trouvé pour votre recherche.</p>
            <?php endif; ?>
        </div> 
    </div> 


<?php include 'footer.php'; ?>
</body>
</html>