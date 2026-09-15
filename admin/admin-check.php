<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "admin") {

    die("Access denied. Admins only.");
}

?>