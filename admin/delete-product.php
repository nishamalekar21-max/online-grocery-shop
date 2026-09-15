<?php

require_once "admin-check.php";
require_once "../config/database.php";

$id = intval($_GET["id"] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare(
        "DELETE FROM products WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();
}

header("Location: products.php");
exit;

?>