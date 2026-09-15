<?php

session_start();

$cart = $_SESSION['cart'] ?? [];

$total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shopping Cart - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .cart-section {
            padding: 50px 7%;
        }

        .cart-section h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 30px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .cart-table th,
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .cart-table th {
            background: #2e7d32;
            color: white;
        }

        .quantity {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .quantity a {
            background: #2e7d32;
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .remove {
            color: red;
            text-decoration: none;
        }

        .cart-total {
            text-align: right;
            margin-top: 25px;
            font-size: 22px;
            font-weight: bold;
        }

        .checkout-btn {
            display: inline-block;
            margin-top: 20px;
            background: #2e7d32;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
        }

        .empty-cart {
            text-align: center;
            font-size: 20px;
            padding: 50px;
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


<section class="cart-section">

    <h1>🛒 Shopping Cart</h1>


    <?php if (empty($cart)): ?>

        <div class="empty-cart">

            <p>Your cart is empty.</p>

            <br>

            <a href="products.php" class="checkout-btn">
                Continue Shopping
            </a>

        </div>

    <?php else: ?>


        <table class="cart-table">

            <tr>

                <th>Product</th>

                <th>Price</th>

                <th>Quantity</th>

                <th>Subtotal</th>

                <th>Action</th>

            </tr>


            <?php foreach ($cart as $id => $item): ?>

                <?php

                $subtotal = $item['price'] * $item['quantity'];

                $total += $subtotal;

                ?>


                <tr>

                    <td>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>

                    <td>
                        Rs. <?php echo number_format($item['price'], 2); ?>
                    </td>

                    <td>

                        <div class="quantity">

                            <a href="update-cart.php?action=decrease&id=<?php echo $id; ?>">
                                −
                            </a>

                            <span>
                                <?php echo $item['quantity']; ?>
                            </span>

                            <a href="update-cart.php?action=increase&id=<?php echo $id; ?>">
                                +
                            </a>

                        </div>

                    </td>

                    <td>
                        Rs. <?php echo number_format($subtotal, 2); ?>
                    </td>

                    <td>

                        <a
                            class="remove"
                            href="update-cart.php?action=remove&id=<?php echo $id; ?>"
                        >
                            Remove
                        </a>

                    </td>

                </tr>


            <?php endforeach; ?>

        </table>


        <div class="cart-total">

            Total:
            Rs. <?php echo number_format($total, 2); ?>

            <br>

            <a href="checkout.php" class="checkout-btn">
                Proceed to Checkout
            </a>

        </div>


    <?php endif; ?>


</section>


<footer>

    <p>© 2026 FreshCart - Online Grocery Shop</p>

</footer>

</body>

</html>