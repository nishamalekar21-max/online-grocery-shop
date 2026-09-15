<?php

session_start();

require_once "config/database.php";

// Customer must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Cart must not be empty
if (empty($_SESSION["cart"])) {
    header("Location: products.php");
    exit;
}

$cart = $_SESSION["cart"];

$total = 0;

foreach ($cart as $item) {
    $total += $item["price"] * $item["quantity"];
}

$message = "";

// When customer submits checkout
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];
    $address = trim($_POST["address"]);

    if ($address === "") {

        $message = "Please enter your delivery address.";

    } else {

        // Start database transaction
        $conn->begin_transaction();

        try {

            // Create order
            $stmt = $conn->prepare(
                "INSERT INTO orders
                (user_id, total_amount, address, status)
                VALUES (?, ?, ?, 'Pending')"
            );

            $stmt->bind_param(
                "ids",
                $user_id,
                $total,
                $address
            );

            $stmt->execute();

            $order_id = $conn->insert_id;


            // Add products to order_items
            $item_stmt = $conn->prepare(
                "INSERT INTO order_items
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)"
            );


            foreach ($cart as $product_id => $item) {

                $quantity = $item["quantity"];
                $price = $item["price"];

                $item_stmt->bind_param(
                    "iiid",
                    $order_id,
                    $product_id,
                    $quantity,
                    $price
                );

                $item_stmt->execute();


                // Reduce stock
                $stock_stmt = $conn->prepare(
                    "UPDATE products
                     SET stock = stock - ?
                     WHERE id = ? AND stock >= ?"
                );

                $stock_stmt->bind_param(
                    "iii",
                    $quantity,
                    $product_id,
                    $quantity
                );

                $stock_stmt->execute();

                if ($stock_stmt->affected_rows === 0) {
                    throw new Exception(
                        "One of the products is out of stock."
                    );
                }
            }


            // Create payment record
            $payment_stmt = $conn->prepare(
                "INSERT INTO payments
                (order_id, payment_method, amount, payment_status)
                VALUES (?, 'Online Payment', ?, 'Pending')"
            );

            $payment_stmt->bind_param(
                "id",
                $order_id,
                $total
            );

            $payment_stmt->execute();


            // Everything successful
            $conn->commit();

            // Empty cart
            $_SESSION["cart"] = [];

            // Save order ID for payment page
            $_SESSION["order_id"] = $order_id;

            // Go to payment page
            header(
                "Location: payment.php?order_id=" . $order_id
            );

            exit;

        } catch (Exception $e) {

            $conn->rollback();

            $message = "Order could not be created: "
                     . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Checkout - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .checkout-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        .checkout-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .checkout-box h2 {
            color: #1b5e20;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group textarea {
            width: 100%;
            min-height: 130px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-size: 22px;
            font-weight: bold;
            color: #2e7d32;
            margin-top: 20px;
        }

        .checkout-btn {
            width: 100%;
            padding: 13px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            background: #2e7d32;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background: #1b5e20;
        }

        .message {
            max-width: 800px;
            margin: 20px auto;
            padding: 15px;
            background: #ffebee;
            color: #b71c1c;
            text-align: center;
        }

        @media (max-width: 700px) {

            .checkout-container {
                grid-template-columns: 1fr;
                margin: 20px;
            }

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
        <a href="my-orders.php">My Orders</a>
        <a href="logout.php">Logout</a>
    </nav>

</header>


<?php if ($message !== ""): ?>

    <div class="message">
        <?php echo htmlspecialchars($message); ?>
    </div>

<?php endif; ?>


<div class="checkout-container">


    <!-- Delivery information -->

    <div class="checkout-box">

        <h2>Delivery Information</h2>

        <form method="POST">

            <div class="form-group">

                <label>Customer Name</label>

                <input
                    type="text"
                    value="<?php echo htmlspecialchars($_SESSION["user_name"]); ?>"
                    disabled
                    style="width:100%; padding:12px;"
                >

            </div>


            <div class="form-group">

                <label>Delivery Address</label>

                <textarea
                    name="address"
                    placeholder="Enter your complete delivery address"
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="checkout-btn"
            >
                Continue to Payment
            </button>

        </form>

    </div>


    <!-- Order summary -->

    <div class="checkout-box">

        <h2>Order Summary</h2>


        <?php foreach ($cart as $item): ?>

            <div class="order-item">

                <span>
                    <?php echo htmlspecialchars($item["name"]); ?>
                    × <?php echo $item["quantity"]; ?>
                </span>

                <span>
                    Rs.
                    <?php
                    echo number_format(
                        $item["price"] * $item["quantity"],
                        2
                    );
                    ?>
                </span>

            </div>

        <?php endforeach; ?>


        <div class="total">

            Total:

            Rs.
            <?php echo number_format($total, 2); ?>

        </div>

    </div>

</div>


<footer>

    <p>© 2026 FreshCart - Online Grocery Shop</p>

</footer>

</body>

</html>