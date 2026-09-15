<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET["order_id"] ?? 0);

if ($order_id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT
        o.id,
        o.total_amount,
        o.status,
        o.created_at,
        p.payment_method,
        p.payment_status,
        p.transaction_id
     FROM orders o
     LEFT JOIN payments p
        ON o.id = p.order_id
     WHERE o.id = ?
     AND o.user_id = ?"
);

$stmt->bind_param(
    "ii",
    $order_id,
    $_SESSION["user_id"]
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Successful - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #e9eceb;
            color: #222;
        }

        .success-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .success-card {
            width: min(760px, 100%);
            background: #f5f5f5;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .success-top {
            background: #ffffff;
            text-align: center;
            padding: 28px 22px 14px;
        }

        .checkmark {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: #d9f5d6;
            color: #28a745;
            font-size: 38px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-title {
            margin: 0;
            font-size: 34px;
            font-weight: 700;
            color: #1b1b1b;
        }

        .success-message {
            margin: 12px 0 8px;
            font-size: 14px;
            color: #333;
        }

        .order-badge {
            display: inline-block;
            background: #2ca84a;
            color: #fff;
            font-weight: 700;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 15px;
            margin-top: 8px;
        }

        .details-box {
            background: #fff;
            padding: 20px 22px 12px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 10px 0 16px;
            color: #1c1c1c;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 16px;
            padding: 14px 0 10px;
            border-top: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 14px;
            color: #2b2b2b;
        }

        .meta-item strong {
            color: #1e1e1e;
        }

        .status-pill {
            display: inline-block;
            background: #f2f2f2;
            border-radius: 4px;
            padding: 2px 8px;
            border: 1px solid #ddd;
        }

        .items-box {
            background: #fff;
            padding: 0 22px 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #dfe6df;
            font-size: 14px;
        }

        .items-table th,
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e7e7e7;
            text-align: left;
        }

        .items-table thead th {
            background: #dff3dc;
            color: #1e2a1f;
            font-weight: 700;
        }

        .items-table tbody tr {
            background: #fff;
        }

        .items-table tfoot td {
            padding: 12px 10px;
            text-align: right;
            font-weight: 700;
            background: #fff;
        }

        .home-btn {
            display: inline-block;
            margin: 18px 0 0;
            background: #2ea94d;
            color: #fff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-weight: 600;
        }

        @media (max-width: 720px) {
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>

<body>

<div class="success-page">
    <div class="success-card">

        <div class="success-top">
            <div class="checkmark">✓</div>
            <h1 class="success-title">Order Confirmed!</h1>
            <p class="success-message">
                Thank you for your purchase! Your order has been successfully placed.
            </p>
            <div class="order-badge">Order ID: #<?php echo $order["id"]; ?></div>
        </div>

        <div class="details-box">
            <div class="section-title">Order Details</div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Order ID:</span>
                    <strong>#<?php echo $order["id"]; ?></strong>
                </div>
                <div class="meta-item">
                    <span>Order Date:</span>
                    <strong>
                        <?php echo date("M j, Y", strtotime($order["created_at"])); ?>
                    </strong>
                </div>
                <div class="meta-item">
                    <span>Total Amount:</span>
                    <strong>Rs. <?php echo number_format($order["total_amount"], 2); ?></strong>
                </div>
                <div class="meta-item">
                    <span>Status:</span>
                    <strong class="status-pill"><?php echo htmlspecialchars($order["status"]); ?></strong>
                </div>
            </div>
        </div>

        <div class="items-box">
            <div class="section-title" style="margin-top: 18px;">Order Items</div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $items_stmt = $conn->prepare(
                        "SELECT p.name, oi.quantity, oi.price
                         FROM order_items oi
                         JOIN products p ON p.id = oi.product_id
                         WHERE oi.order_id = ?"
                    );
                    $items_stmt->bind_param("i", $order["id"]);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();
                    ?>

                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item["name"]); ?></td>
                            <td><?php echo (int) $item["quantity"]; ?></td>
                            <td>Rs. <?php echo number_format((float) $item["price"], 2); ?></td>
                            <td>Rs. <?php echo number_format((float) $item["price"] * (int) $item["quantity"], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Total:</td>
                        <td>Rs. <?php echo number_format((float) $order["total_amount"], 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <a href="index.php" class="home-btn">Continue Shopping</a>
        </div>

    </div>
</div>

</body>

</html>