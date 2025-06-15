<?php 
session_start();

include 'bdd.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, name, image, price FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

$cart_summary = [];
$total = 0;

foreach ($cart_items as $item_id) {
    if (isset($cart_summary[$item_id])) {
        $cart_summary[$item_id]['quantity']++;
    } else {
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

foreach ($cart_summary as $item) {
    $total += $item['product']['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalisation de l'Achat - Catalogue Produits</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">

    <style>

main h1 {
    padding-top: 650px;
}

html, body {
    height: 100%;
    overflow-x: hidden;
    background-color: #f9f9f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
}

.checkout-container {
    display: flex;
    flex-direction: column;
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

main h1 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2em;
    color: #007acc;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
}

.summary-table th,
.summary-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.summary-table thead {
    background-color: #007acc;
    color: white;
}

.total-row td {
    background-color: #f1f1f1;
    font-size: 1.1em;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}

.checkout-form-section {
    background-color: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
}

.checkout-form fieldset {
    border: none;
    margin-bottom: 20px;
}

legend {
    font-weight: bold;
    font-size: 1.2em;
    margin-bottom: 15px;
    color: #007acc;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
}

.btn-primary {
    background-color: #007acc;
    color: white;
}

.btn-secondary {
    background-color: #e0e0e0;
    color: #333;
}

.error-message {
    color: red;
    font-size: 0.85em;
    margin-top: 4px;
}

.required {
    color: red;
}


    </style>


</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <h1>Finalisation de l'Achat</h1>

    <div class="checkout-container">
        <!-- Résumé de la commande -->
        <section class="order-summary">
            <h2>Résumé de votre commande</h2>
            <div class="cart-summary">
                <?php if (!empty($cart_summary)): ?>
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th scope="col">Produit</th>
                                <th scope="col">Quantité</th>
                                <th scope="col">Prix unitaire</th>
                                <th scope="col">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_summary as $item): ?>
                                <?php $subtotal = $item['product']['price'] * $item['quantity']; ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="<?php echo htmlspecialchars($item['product']['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product']['name']); ?>" 
                                                 class="product-thumbnail">
                                            <span><?php echo htmlspecialchars($item['product']['name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>€<?php echo number_format($item['product']['price'], 2, ',', ' '); ?></td>
                                    <td>€<?php echo number_format($subtotal, 2, ',', ' '); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3"><strong>Total</strong></td>
                                <td><strong>€<?php echo number_format($total, 2, ',', ' '); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        <!-- Formulaire de commande -->
        <section class="checkout-form-section">
            <h2>Informations de livraison et paiement</h2>
            
            <form method="post" action="processor_order.php" class="checkout-form" novalidate>
                <fieldset class="delivery-info">
                    <legend>Informations de livraison</legend>
                    
                    <div class="form-group">
                        <label for="full_name">Nom complet <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" required 
                               placeholder="Votre nom et prénom">
                        <span class="error-message" id="full_name_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required 
                               placeholder="votre@email.com">
                        <span class="error-message" id="email_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" 
                               placeholder="01 23 45 67 89">
                    </div>

                    <div class="form-group">
                        <label for="address">Adresse de livraison <span class="required">*</span></label>
                        <textarea id="address" name="address" required rows="3" 
                                  placeholder="Numéro, rue, ville, code postal"></textarea>
                        <span class="error-message" id="address_error"></span>
                    </div>
                </fieldset>

                <fieldset class="payment-info">
                    <legend>Méthode de paiement</legend>
                    
                    <div class="form-group">
                        <label for="payment_method">Choisissez votre méthode de paiement <span class="required">*</span></label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="credit_card">Carte de crédit</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Virement bancaire</option>
                        </select>
                        <span class="error-message" id="payment_method_error"></span>
                    </div>

                    <div class="payment-details" id="credit_card_details" style="display: none;">
                        <div class="form-group">
                            <label for="card_number">Numéro de carte</label>
                            <input type="text" id="card_number" name="card_number" 
                                   placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry_date">Date d'expiration</label>
                                <input type="text" id="expiry_date" name="expiry_date" 
                                       placeholder="MM/AA" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" 
                                       placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <a href="cart.php" class="btn btn-secondary">← Retourner au panier</a>
                    <button type="submit" class="btn btn-primary">
                        Finaliser l'Achat (€<?php echo number_format($total, 2, ',', ' '); ?>)
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.checkout-form');
    const paymentSelect = document.getElementById('payment_method');
    const creditCardDetails = document.getElementById('credit_card_details');
    
    paymentSelect.addEventListener('change', function() {
        if (this.value === 'credit_card') {
            creditCardDetails.style.display = 'block';
            document.getElementById('card_number').required = true;
            document.getElementById('expiry_date').required = true;
            document.getElementById('cvv').required = true;
        } else {
            creditCardDetails.style.display = 'none';
            document.getElementById('card_number').required = false;
            document.getElementById('expiry_date').required = false;
            document.getElementById('cvv').required = false;
        }
    });

    const cardNumberInput = document.getElementById('card_number');
    cardNumberInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, ''); 
        value = value.replace(/(\d{4})(?=\d)/g, '$1 '); 
        this.value = value;
    });

    const expiryInput = document.getElementById('expiry_date');
    expiryInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        this.value = value;
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        document.querySelectorAll('.error-message').forEach(span => {
            span.textContent = '';
        });

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                const errorSpan = document.getElementById(field.name + '_error');
                if (errorSpan) {
                    errorSpan.textContent = 'Ce champ est requis.';
                }
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        const emailField = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailPattern.test(emailField.value)) {
            isValid = false;
            document.getElementById('email_error').textContent = 'Veuillez entrer un email valide.';
            emailField.classList.add('error');
        }

        if (!isValid) {
            e.preventDefault();
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    form.addEventListener('input', function(e) {
        if (e.target.classList.contains('error')) {
            e.target.classList.remove('error');
            const errorSpan = document.getElementById(e.target.name + '_error');
            if (errorSpan) {
                errorSpan.textContent = '';
            }
        }
    });
});

window.addEventListener('load', () => {
  const main = document.querySelector('main');
  const productRows = document.querySelectorAll('.summary-table tbody tr');
  const basePadding = 100;

  if (main) {
    const count = productRows.length;
    const padding = count * basePadding;
    main.style.paddingTop = padding + 'px';
  }
});


</script>

</body>
</html>