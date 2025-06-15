<?php
session_start();

$products = [
    ["id" => 1, "name" => "Produit 1", "image" => "image/produit1.jpeg", "price" => 20],
    ["id" => 2, "name" => "Produit 2", "image" => "image/produit2.jpeg", "price" => 25],
    ["id" => 3, "name" => "Produit 3", "image" => "image/produit3.jpeg", "price" => 30],
    //Exemple
];

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php include 'navbar.php';?>

<h1>Votre Panierr</h1>

<?php if (!empty($_cart_items)): ?>
    <ul>
        <?php foreach ($cart_items as $item_id): ?>
            <?php
            $product = array_filter($products, function($prod) use ($item_id) {
                return $prod['id'] == $item_id;
            });
            $product = array_values($product)[0];
            $total += $product['price'];
            ?>
            <li>
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" with="100">
                <?php echo $product['name']; ?> - €<?php echo $product['price']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <p>Total: €<?php echo $total; ?></p>
    <form method="post">
        <button type="submit" name="clear_cart">Vider le Panier</button>
    </form>
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>

    <a href="index.php2">retourner au Catalogue</a>

    <?php include 'footer.php'; ?>
</body>
</html>