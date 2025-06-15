<?php
session_start();

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];


    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $product_id;

    header("Location: cart.php");
    exit;
} else {
    header("Location: index2.php");
    exit;
}
?>

