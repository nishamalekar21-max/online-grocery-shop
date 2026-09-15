<?php

require_once "admin-check.php";
require_once "../config/database.php";

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories
        ON products.category_id = categories.id
        ORDER BY products.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Products - FreshCart</title>

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

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .top h1 {
            color: #1b5e20;
        }

        .add-btn {
            background: #2e7d32;
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 5px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2e7d32;
            color: white;
        }

        .edit {
            color: #1565c0;
            text-decoration: none;
            margin-right: 10px;
        }

        .delete {
            color: #c62828;
            text-decoration: none;
        }

    </style>

</head>

<body>


<div class="sidebar">

    <h2>🛒 FreshCart</h2>

    <a href="dashboard.php">Dashboard</a>

    <a href="products.php">Products</a>

    <a href="orders.php">Orders</a>

    <a href="users.php">Users</a>

    <a href="../index.php">View Website</a>

    <a href="../logout.php">Logout</a>

</div>


<div class="main">

    <div class="top">

        <h1>Manage Products</h1>

        <a href="add-product.php" class="add-btn">
            + Add Product
        </a>

    </div>


    <table>

        <tr>

            <th>ID</th>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>

        </tr>


        <?php while ($product = $result->fetch_assoc()): ?>

            <tr>

                <td>
                    <?php echo $product["id"]; ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $product["name"]
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $product["category_name"] ?? "-"
                    );
                    ?>
                </td>

                <td>
                    Rs.
                    <?php
                    echo number_format(
                        $product["price"],
                        2
                    );
                    ?>
                </td>

                <td>
                    <?php echo $product["stock"]; ?>
                </td>

                <td>

                    <a
                        class="edit"
                        href="edit-product.php?id=<?php echo $product["id"]; ?>"
                    >
                        Edit
                    </a>

                    <a
                        class="delete"
                        href="delete-product.php?id=<?php echo $product["id"]; ?>"
                        onclick="return confirm('Delete this product?');"
                    >
                        Delete
                    </a>

                </td>

            </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>

</html>