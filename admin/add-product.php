<?php

require_once "admin-check.php";
require_once "../config/database.php";

$message = "";

$categories = $conn->query(
    "SELECT id, name FROM categories ORDER BY name"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $category_id = intval($_POST["category_id"]);

    if (
        $name === "" ||
        $price <= 0 ||
        $stock < 0 ||
        $category_id <= 0
    ) {

        $message = "Please enter valid product details.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO products
            (category_id, name, description, price, stock)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issdi",
            $category_id,
            $name,
            $description,
            $price,
            $stock
        );

        if ($stmt->execute()) {

            header("Location: products.php");
            exit;

        } else {

            $message = "Unable to add product.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Add Product - FreshCart</title>

    <style>

        body {
            font-family: Arial;
            background: #f5f6f5;
            padding: 40px;
        }

        .box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            color: #1b5e20;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 100px;
        }

        button {
            margin-top: 25px;
            padding: 12px 25px;
            background: #2e7d32;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            background: #ffebee;
            padding: 12px;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>Add Grocery Product</h1>


    <?php if ($message): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Product Name</label>

        <input
            type="text"
            name="name"
            required
        >


        <label>Category</label>

        <select name="category_id" required>

            <option value="">
                Select Category
            </option>

            <?php while ($category = $categories->fetch_assoc()): ?>

                <option value="<?php echo $category["id"]; ?>">

                    <?php
                    echo htmlspecialchars(
                        $category["name"]
                    );
                    ?>

                </option>

            <?php endwhile; ?>

        </select>


        <label>Description</label>

        <textarea
            name="description"
        ></textarea>


        <label>Price (Rs.)</label>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0"
            required
        >


        <label>Stock</label>

        <input
            type="number"
            name="stock"
            min="0"
            required
        >


        <button type="submit">
            Add Product
        </button>

    </form>

</div>

</body>

</html>