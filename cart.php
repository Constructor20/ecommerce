<?php
session_start();

include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, name, image, video, price FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $product_id;
    header("Location: cart.php");
    exit;
}

if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];

    if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $key = array_search($item_id, $_SESSION['cart']);

        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
    }

    header("Location: cart.php");
    exit;
}

// Calcul des quantités pour éviter les doublons dans l'affichage
$cart_summary = [];
foreach ($cart_items as $item_id) {
    if (isset($cart_summary[$item_id])) {
        $cart_summary[$item_id]['quantity']++;
    } else {
        // Trouver le produit correspondant
        $product = null;
        foreach ($products as $prod) {
            if ($prod['id'] == $item_id) {
                $product = $prod;
                break;
            }
        }
        
        if ($product) {
            $cart_summary[$item_id] = [
                'product' => $product,
                'quantity' => 1
            ];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Catalogue Produits</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">

    <style>

html, body {
    height: auto;
    min-height: 100%;
    overflow-y: auto;
}

main {
    padding-top: 80px; /* hauteur du header */
}


.cart-container {
    overflow-x: auto;
    overflow-y: visible;
    max-width: 100%;
    margin-bottom: 2rem;
}

.cart-item-image {
    width: 100px;
    height: 100px;
    min-width: 80px;
    min-height: 80px;
    object-fit: cover;
    border-radius: 6px;
    display: block;
}

.cart-table th {
  padding-left: 20px;
  padding-right: 20px;
}

.cart-table th {
  padding: 10px 20px; /* 10px vertical, 20px horizontal */
}



    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

<main>
    <h1>Votre Panier</h1>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Nom du Produit</th>
                        <th scope="col">Prix Unitaire</th>
                        <th scope="col">Quantité</th>
                        <th scope="col">Sous-total</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_summary as $item_id => $item): ?>
                        <?php 
                        $product = $item['product'];
                        $quantity = $item['quantity'];
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="cart-item-image"
                                     loading="lazy">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                            </td>
                            <td>
                                €<?php echo number_format($product['price'], 2, ',', ' '); ?>
                            </td>
                            <td>
                                <span class="quantity"><?php echo $quantity; ?></span>
                            </td>
                            <td>
                                <strong>€<?php echo number_format($subtotal, 2, ',', ' '); ?></strong>
                            </td>
                            <td class="actions">
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-add" title="Ajouter une unité">+</button>
                                </form>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
                                    <button type="submit" name="remove_item" class="btn btn-remove" title="Supprimer une unité">-</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>€<?php echo number_format($total, 2, ',', ' '); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <!-- Bouton vider panier -->
                <form method="post" class="clear-cart-form">
                    <button type="submit" name="clear_cart" class="btn btn-danger" 
                            onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                        Vider le Panier
                    </button>
                </form>

                <!-- Bouton finaliser la commande -->
                <form method="get" action="checkout.php" class="checkout-form">
                    <button type="submit" class="btn btn-primary">
                        Finaliser la Commande (€<?php echo number_format($total, 2, ',', ' '); ?>)
                    </button>
                    <button onclick="window.location.href='index2.php'" class="btn btn-secondary" style="margin-top: 10px;">
                    ← Retourner au Catalogue
                    </button>

                </form>
            </div>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <p class="empty-message">Votre panier est vide</p>
            <img src="images/empty-cart.svg" alt="Panier vide" class="empty-cart-icon" style="display: none;">
            
            <button onclick="window.location.href='index2.php'" class="btn btn-secondary" style="margin-top: 10px;">
                ← Retourner au Catalogue
            </button>
        </div>
    <?php endif; ?>

</main>


    <?php include 'footer.php'; ?>

    <script>
        // Confirmation avant de vider le panier
        document.addEventListener('DOMContentLoaded', function() {
            const clearCartBtn = document.querySelector('button[name="clear_cart"]');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir vider complètement votre panier ?')) {
                        e.preventDefault();
                    }
                });
            }

            // Animation des boutons +/-
            const actionButtons = document.querySelectorAll('.actions button');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });
        });
    </script>
        
</body>
</html>
