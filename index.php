<?php

session_start();

require_once "config/database.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshCart - Online Grocery Shop</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <div class="logo">
        🛒 FreshCart
    </div>

    <nav>

    <a href="index.php">Home</a>

    <a href="products.php">Products</a>

    <a href="cart.php">Cart</a>

    <?php if (isset($_SESSION["user_id"])): ?>

        <a href="my-orders.php">
            My Orders
        </a>

        <a href="logout.php">
            Logout
        </a>

    <?php else: ?>

        <a href="login.php">
            Login
        </a>

        <a href="register.php">
            Register
        </a>

    <?php endif; ?>

</nav>
</header>

<section class="hero">

    <div class="hero-content">
        <h1>Fresh Groceries Delivered to Your Door</h1>

        <p>
            Shop fresh fruits, vegetables, dairy products,
            beverages and more from the comfort of your home.
        </p>

        <a href="products.php" class="btn">
            Shop Now
        </a>
    </div>

</section>

<section class="categories">

    <h2>Shop by Category</h2>

    <div class="category-container">

        <div class="category">🍎<h3>Fruits</h3></div>
        <div class="category">🥦<h3>Vegetables</h3></div>
        <div class="category">🥛<h3>Dairy</h3></div>
        <div class="category">🍞<h3>Bakery</h3></div>
        <div class="category">🥤<h3>Beverages</h3></div>
        <div class="category">🍪<h3>Snacks</h3></div>

    </div>

</section>

<section class="features">

    <div>
        <h3>🚚 Fast Delivery</h3>
        <p>Get your groceries delivered quickly.</p>
    </div>

    <div>
        <h3>🔒 Secure Payment</h3>
        <p>Safe and secure online payment.</p>
    </div>

    <div>
        <h3>🥬 Fresh Products</h3>
        <p>Quality groceries at affordable prices.</p>
    </div>

</section>

<footer>
    <p>© 2026 FreshCart - Online Grocery Shop</p>
</footer>

</body>
</html>