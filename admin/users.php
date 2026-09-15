<?php

require_once "admin-check.php";
require_once "../config/database.php";

$result = $conn->query(
    "SELECT id, name, email, phone, address, role, created_at
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Users - FreshCart Admin</title>

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
            margin-bottom: 30px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        th,
        td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2e7d32;
            color: white;
        }

        .admin {
            color: #1565c0;
            font-weight: bold;
        }

        .customer {
            color: #555;
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

    <h1>👥 Registered Users</h1>


    <div class="table-container">

        <table>

            <tr>

                <th>ID</th>

                <th>Name</th>

                <th>Email</th>

                <th>Phone</th>

                <th>Address</th>

                <th>Role</th>

                <th>Registered</th>

            </tr>


            <?php while ($user = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $user["id"]; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $user["name"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $user["email"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $user["phone"] ?? "-"
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $user["address"] ?? "-"
                            )
                        );
                        ?>
                    </td>


                    <td>

                        <span class="<?php
                            echo $user["role"] === "admin"
                                ? "admin"
                                : "customer";
                        ?>">

                            <?php
                            echo htmlspecialchars(
                                $user["role"]
                            );
                            ?>

                        </span>

                    </td>


                    <td>

                        <?php
                        echo date(
                            "d M Y",
                            strtotime(
                                $user["created_at"]
                            )
                        );
                        ?>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>

</html>