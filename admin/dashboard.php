<?php

require_once "admin-check.php";
require_once "../config/database.php";


$user_count = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
)->fetch_assoc()["total"];


$product_count = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
)->fetch_assoc()["total"];


$order_count = $conn->query(
    "SELECT COUNT(*) AS total FROM orders"
)->fetch_assoc()["total"];


$payment_count = $conn->query(
    "SELECT COUNT(*) AS total
     FROM payments
     WHERE payment_status = 'Paid'"
)->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - FreshCart</title>

    <style>

        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f5f6f5;
        }

        .sidebar {
            position: fixed;
            width: 230px;
            height: 100vh;
            background: #1b5e20;
            color: white;
            padding: 25px;
        }

        .sidebar h2 {
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 0;
        }

        .sidebar a:hover {
            text-decoration: underline;
        }

        .main {
            margin-left: 230px;
            padding: 40px;
        }

        .main h1 {
            color: #1b5e20;
        }

        .cards {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(200px, 1fr));

            gap: 25px;
            margin-top: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow:
                0 3px 12px rgba(0,0,0,0.1);
        }

        .card h2 {
            font-size: 35px;
            margin: 10px 0;
            color: #2e7d32;
        }

        .card p {
            color: #666;
        }

    </style>

</head>

<body>


<div class="sidebar">

    <h2>🛒 FreshCart</h2>

    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="orders.php">
        Orders
    </a>

    <a href="products.php">
        Products
    </a>

    <a href="users.php">
        Users
    </a>

    <a href="../index.php">
        View Website
    </a>

    <a href="../logout.php">
        Logout
    </a>

</div>


<div class="main">

    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <strong>
            <?php
            echo htmlspecialchars(
                $_SESSION["user_name"]
            );
            ?>
        </strong>
    </p>


    <div class="cards">


        <div class="card">

            <p>Total Users</p>

            <h2>
                <?php echo $user_count; ?>
            </h2>

        </div>


        <div class="card">

            <p>Total Products</p>

            <h2>
                <?php echo $product_count; ?>
            </h2>

        </div>


        <div class="card">

            <p>Total Orders</p>

            <h2>
                <?php echo $order_count; ?>
            </h2>

        </div>


        <div class="card">

            <p>Paid Payments</p>

            <h2>
                <?php echo $payment_count; ?>
            </h2>

        </div>


    </div>

</div>

</body>

</html>