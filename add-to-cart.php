<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['id'])) {

    $product_id = intval($_GET['id']);

    $sql = "SELECT id, name, price, stock
            FROM products
            WHERE id = $product_id";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {

        $product = $result->fetch_assoc();

        if ($product['stock'] > 0) {

            if (isset($_SESSION['cart'][$product_id])) {

                if ($_SESSION['cart'][$product_id]['quantity'] < $product['stock']) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                }

            } else {

                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'stock' => $product['stock']
                ];
            }
        }
    }
}

header("Location: cart.php");
exit;

?>