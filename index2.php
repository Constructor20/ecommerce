<?php session_start();

include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $priceQuery = $pdo->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products");
    $priceResult = $priceQuery->fetch(PDO::FETCH_ASSOC);
    $minPrice = $priceResult['min_price'] ?? 0;
    $maxPrice = $priceResult['max_price'] ?? 35.00;

    $sql = "SELECT id, name, image, video, price FROM products WHERE 1=1";

    if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
        $min_price = (float) $_GET['price_min'];
        $max_price = (float) $_GET['price_max'];
        $sql .= " AND price BETWEEN $min_price AND $max_price";
    }

    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $product_id;
    header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Produits</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
</head> 

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
    height: 200px; /* ou 200px selon la hauteur souhaitée */
    border-radius: 6px;
    object-fit: cover; /* évite la déformation */
    display: block;
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



</style>
<body>
    
<?php include 'navbar.php';?>

<div class="catalogue-module">
    <h1 style="text-align: center; margin-top: 20px;">Propositions de cours en PHP</h1>
    <div class="product-list">
    <?php foreach ($products as $product): ?>
        <div class="product-item">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php
            $video = $product['video'];
            if (strpos($video, 'youtube.com') !== false || strpos($video, 'youtu.be') !== false):
                parse_str(parse_url($video, PHP_URL_QUERY), $ytParams);
                $videoId = $ytParams['v'] ?? basename(parse_url($video, PHP_URL_PATH));
            ?>
                <iframe 
                    width="100%" 
                    height="200" 
                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>" 
                    frameborder="0" 
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <video controls>
                    <source src="<?php echo htmlspecialchars($video); ?>" type="video/mp4">
                    Votre navigateur ne supporte pas les vidéos.
                </video>
            <?php endif; ?>
            <h2>
                <a href="video.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                    <?php echo htmlspecialchars($product['name']); ?>
                </a>
            </h2>
            <p>Prix: €<?php echo htmlspecialchars($product['price']); ?></p>
            <form method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <button type="submit" name="add_to_cart">Ajouter au Panier</button>
            </form>
        </div>
    <?php endforeach; ?>
</div> <!-- fin de .product-list -->
            <a href="cart.php">Voir le Panier</a>
    <!-- Formulaire de filtrage en dessous -->
    <form method="get" id="filterForm">
        <label>
            <input type="checkbox" name="filter_php" <?php echo isset($_GET['filter_php']) ? 'checked' : ''; ?>>
            En PHP&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="filter_css" <?php echo isset($_GET['filter_css']) ? 'checked' : ''; ?>>
            En CSS&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="filter_js" <?php echo isset($_GET['filter_js']) ? 'checked' : ''; ?>>
            En JS&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="filter_mysql" <?php echo isset($_GET['filter_mysql']) ? 'checked' : ''; ?>>
            En MySQL
        </label>
        <div class="price-slider">
            <input type="range" name="price_min" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?>" step="1" style="width: 45%;" id="minPrice">&nbsp;&nbsp;&nbsp;
            <input type="range" name="price_max" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?>" step="1" style="width: 45%;" id="maxPrice">
        </div>
        <div class="price-values">
            <span>Prix min: <span id="price-min"><?php echo isset($_GET['price_min']) ? $_GET['price_min'] : $minPrice; ?></span></span>
            <span>Prix max: <span id="price-max"><?php echo isset($_GET['price_max']) ? $_GET['price_max'] : $maxPrice; ?></span></span>
        </div>
    </form>
        <?php include 'footer.php';?>

<script>

    const minSlider = document.getElementById('minPrice');
    const maxSlider = document.getElementById('maxPrice');
    const minPriceLabel = document.getElementById('price-min');
    const maxPriceLabel = document.getElementById('price-max');
    const filterForm = document.getElementById('filterForm');

    minSlider.addEventListener('input', function() {
        if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
            maxSlider.value = minSlider.value;
        }
        minPriceLabel.textContent = minSlider.value;
        filterForm.submit();
    });

    maxSlider.addEventListener('input', function() {
        if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
            minSlider.value = maxSlider.value;
        }
        maxPriceLabel.textContent = maxSlider.value;
        filterForm.submit();
    });

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            filterForm.submit()
        });
    });
</script>

</body>
</html>