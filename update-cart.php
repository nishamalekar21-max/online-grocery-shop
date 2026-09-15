<?php

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if (isset($_SESSION['cart'][$id])) {

    if ($action === 'increase') {

        if (
            $_SESSION['cart'][$id]['quantity']
            < $_SESSION['cart'][$id]['stock']
        ) {
            $_SESSION['cart'][$id]['quantity']++;
        }

    } elseif ($action === 'decrease') {

        $_SESSION['cart'][$id]['quantity']--;

        if ($_SESSION['cart'][$id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$id]);
        }

    } elseif ($action === 'remove') {

        unset($_SESSION['cart'][$id]);
    }
}

header("Location: cart.php");
exit;

?>