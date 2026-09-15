<?php

require_once "admin-check.php";
require_once "../config/database.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$message = "";

/*
 * Get existing product
 */
$stmt = $conn->prepare(
    "SELECT id, category_id, name, description, price, stock
     FROM products
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Product not found.");
}

$product = $result->fetch_assoc();


/*
 * Get categories
 */
$categories = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name"
);


/*
 * Update product
 */
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

        $update = $conn->prepare(
            "UPDATE products
             SET category_id = ?,
                 name = ?,
                 description = ?,
                 price = ?,
                 stock = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "issdii",
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $id
        );

        if ($update->execute()) {

            header("Location: products.php");
            exit;

        } else {

            $message = "Product could not be updated.";
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

    <title>Edit Product - FreshCart</title>

    <style>

        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f5f6f5;
            padding: 40px;
        }

        .box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        h1 {
            color: #1b5e20;
            margin-bottom: 25px;
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
            resize: vertical;
        }

        button {
            margin-top: 25px;
            padding: 12px 25px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            padding: 12px;
            background: #ffebee;
            color: #b71c1c;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            color: #2e7d32;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>✏️ Edit Product</h1>

    <?php if ($message !== ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Product Name</label>

        <input
            type="text"
            name="name"
            value="<?php
            echo htmlspecialchars($product["name"]);
            ?>"
            required
        >


        <label>Category</label>

        <select name="category_id" required>

            <?php while ($category = $categories->fetch_assoc()): ?>

                <option
                    value="<?php echo $category["id"]; ?>"
                    <?php
                    if (
                        $category["id"]
                        == $product["category_id"]
                    ) {
                        echo "selected";
                    }
                    ?>
                >

                    <?php
                    echo htmlspecialchars(
                        $category["name"]
                    );
                    ?>

                </option>

            <?php endwhile; ?>

        </select>


        <label>Description</label>

        <textarea name="description"><?php
            echo htmlspecialchars(
                $product["description"]
            );
        ?></textarea>


        <label>Price (Rs.)</label>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0.01"
            value="<?php echo $product["price"]; ?>"
            required
        >


        <label>Stock</label>

        <input
            type="number"
            name="stock"
            min="0"
            value="<?php echo $product["stock"]; ?>"
            required
        >


        <button type="submit">
            Update Product
        </button>

    </form>


    <a href="products.php" class="back">
        ← Back to Products
    </a>

</div>

</body>

</html>