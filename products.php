<?php
require_once "config/database.php";

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        ORDER BY products.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .products-section {
            padding: 50px 7%;
        }

        .products-section h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 35px;
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .product-image {
    height: 150px;
    background: #e8f5e9;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin-bottom: 15px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

        .product-card h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .category-name {
            color: #777;
            font-size: 14px;
        }

        .description {
            margin: 12px 0;
            color: #555;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #2e7d32;
            margin: 10px;
        }

        .stock {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .add-cart {
            display: inline-block;
            background: #2e7d32;
            color: white;
            padding: 10px 18px;
            border-radius: 5px;
            text-decoration: none;
        }

        .add-cart:hover {
            background: #1b5e20;
        }
    </style>
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
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>

</header>

<section class="products-section">

    <h1>Our Grocery Products</h1>

    <div class="product-container">

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($product = $result->fetch_assoc()): ?>

                <div class="product-card">

                    <div class="product-image">

    <?php if (!empty($product['image'])): ?>

        <img
            src="images/products/<?php echo htmlspecialchars($product['image']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>"
        >

    <?php else: ?>

        🛒

    <?php endif; ?>

</div>

                    <h2>
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h2>

                    <div class="category-name">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </div>

                    <p class="description">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>

                    <div class="price">
                        Rs. <?php echo number_format($product['price'], 2); ?>
                    </div>

                    <div class="stock">
                        Stock: <?php echo $product['stock']; ?>
                    </div>

                    <?php if ($product['stock'] > 0): ?>

                        <a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="add-cart">
    Add to Cart
</a>

                    <?php else: ?>

                        <span>
                            Out of Stock
                        </span>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>No products available.</p>

        <?php endif; ?>

    </div>

</section>

<footer>
    <p>© 2026 FreshCart - Online Grocery Shop</p>
</footer>

</body>
</html>