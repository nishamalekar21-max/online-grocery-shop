<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT 
        o.id,
        o.total_amount,
        o.status,
        o.address,
        o.created_at,
        p.payment_method,
        p.payment_status,
        p.transaction_id
     FROM orders o
     LEFT JOIN payments p
        ON o.id = p.order_id
     WHERE o.user_id = ?
     ORDER BY o.id DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Orders - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .orders-section {
            padding: 50px 7%;
        }

        .orders-section h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 30px;
        }

        .order-card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .order-info p {
            margin: 8px 0;
        }

        .status {
            font-weight: bold;
            color: #2e7d32;
        }

        .payment {
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 50px;
            background: white;
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


<section class="orders-section">

    <h1>📦 My Orders</h1>


    <?php if ($result->num_rows > 0): ?>

        <?php while ($order = $result->fetch_assoc()): ?>

            <div class="order-card">

                <div class="order-header">

                    <strong>
                        Order #<?php echo $order["id"]; ?>
                    </strong>

                    <span>
                        <?php
                        echo date(
                            "d M Y",
                            strtotime($order["created_at"])
                        );
                        ?>
                    </span>

                </div>


                <div class="order-info">

                    <p>
                        <strong>Total:</strong>
                        Rs.
                        <?php
                        echo number_format(
                            $order["total_amount"],
                            2
                        );
                        ?>
                    </p>

                    <p>
                        <strong>Order Status:</strong>

                        <span class="status">
                            <?php
                            echo htmlspecialchars(
                                $order["status"]
                            );
                            ?>
                        </span>
                    </p>

                    <p>
                        <strong>Payment:</strong>

                        <span class="payment">
                            <?php
                            echo htmlspecialchars(
                                $order["payment_status"] ?? "Pending"
                            );
                            ?>
                        </span>
                    </p>

                    <p>
                        <strong>Payment Method:</strong>

                        <?php
                        echo htmlspecialchars(
                            $order["payment_method"] ?? "-"
                        );
                        ?>
                    </p>

                    <?php if (!empty($order["transaction_id"])): ?>

                        <p>
                            <strong>Transaction ID:</strong>

                            <?php
                            echo htmlspecialchars(
                                $order["transaction_id"]
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <p>
                        <strong>Delivery Address:</strong>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $order["address"]
                            )
                        );
                        ?>
                    </p>

                </div>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="empty">

            <h2>No Orders Yet</h2>

            <p>Start shopping to place your first order.</p>

            <br>

            <a href="products.php" class="btn">
                Shop Now
            </a>

        </div>

    <?php endif; ?>

</section>


<footer>

    <p>© 2026 FreshCart - Online Grocery Shop</p>

</footer>

</body>

</html>