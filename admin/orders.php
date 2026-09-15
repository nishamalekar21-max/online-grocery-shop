<?php

require_once "admin-check.php";
require_once "../config/database.php";

$message = "";

// Update order status
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $order_id = intval($_POST["order_id"]);
    $status = $_POST["status"];

    $allowed_statuses = [
        "Pending",
        "Confirmed",
        "Shipped",
        "Delivered",
        "Cancelled"
    ];

    if (
        $order_id > 0 &&
        in_array($status, $allowed_statuses, true)
    ) {

        $stmt = $conn->prepare(
            "UPDATE orders
             SET status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $status,
            $order_id
        );

        $stmt->execute();

        $message = "Order status updated.";
    }
}


// Get all orders
$sql = "SELECT
            o.id,
            u.name AS customer_name,
            u.email,
            o.total_amount,
            o.status,
            o.address,
            o.created_at,
            p.payment_method,
            p.payment_status,
            p.transaction_id
        FROM orders o

        INNER JOIN users u
            ON o.user_id = u.id

        LEFT JOIN payments p
            ON o.id = p.order_id

        ORDER BY o.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Orders - FreshCart</title>

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

        .main {
            margin-left: 230px;
            padding: 40px;
        }

        h1 {
            color: #1b5e20;
        }

        .message {
            background: #e8f5e9;
            padding: 15px;
            margin: 20px 0;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1000px;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2e7d32;
            color: white;
        }

        select {
            padding: 7px;
        }

        button {
            margin-top: 5px;
            padding: 7px 12px;
            background: #2e7d32;
            color: white;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
        }

        .paid {
            color: #2e7d32;
            font-weight: bold;
        }

        .pending {
            color: #ef6c00;
            font-weight: bold;
        }

    </style>

</head>

<body>


<div class="sidebar">

    <h2>🛒 FreshCart</h2>

    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="products.php">
        Products
    </a>

    <a href="orders.php">
        Orders
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

    <h1>Manage Orders</h1>


    <?php if ($message): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="table-container">

        <table>

            <tr>

                <th>Order</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Address</th>
                <th>Status</th>
                <th>Update</th>

            </tr>


            <?php while ($order = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        #<?php echo $order["id"]; ?>
                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $order["customer_name"]
                        );
                        ?>

                        <br>

                        <small>
                            <?php
                            echo htmlspecialchars(
                                $order["email"]
                            );
                            ?>
                        </small>

                    </td>


                    <td>

                        Rs.
                        <?php
                        echo number_format(
                            $order["total_amount"],
                            2
                        );
                        ?>

                    </td>


                    <td>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $order["payment_method"] ?? "-"
                            );
                            ?>
                        </strong>

                        <br>

                        <span class="<?php
                            echo
                            ($order["payment_status"] === "Paid")
                            ? "paid"
                            : "pending";
                        ?>">

                            <?php
                            echo htmlspecialchars(
                                $order["payment_status"] ?? "Pending"
                            );
                            ?>

                        </span>

                    </td>


                    <td>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $order["address"]
                            )
                        );
                        ?>

                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $order["status"]
                        );
                        ?>

                    </td>


                    <td>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?php
                                echo $order["id"];
                                ?>"
                            >


                            <select name="status">

                                <?php

                                $statuses = [
                                    "Pending",
                                    "Confirmed",
                                    "Shipped",
                                    "Delivered",
                                    "Cancelled"
                                ];

                                foreach ($statuses as $status):

                                ?>

                                    <option
                                        value="<?php echo $status; ?>"
                                        <?php
                                        if (
                                            $order["status"]
                                            === $status
                                        ) {
                                            echo "selected";
                                        }
                                        ?>
                                    >

                                        <?php echo $status; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>


                            <br>

                            <button type="submit">
                                Update
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>

</html>