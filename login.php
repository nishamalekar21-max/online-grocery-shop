<?php

session_start();
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_role"] = $user["role"];

// Redirect based on user role
if ($user["role"] === "admin") {

    header("Location: admin/dashboard.php");
    exit;

} else {

    header("Location: index.php");
    exit;
}

        } else {

            $message = "Incorrect password.";
        }

    } else {

        $message = "No account found with this email.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .auth-container {
            max-width: 450px;
            margin: 70px auto;
            padding: 35px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .auth-container h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .auth-btn {
            width: 100%;
            padding: 13px;
            border: none;
            background: #2e7d32;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .auth-btn:hover {
            background: #1b5e20;
        }

        .message {
            background: #ffebee;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #2e7d32;
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
        <a href="register.php">Register</a>
    </nav>

</header>


<div class="auth-container">

    <h1>Login</h1>


    <?php if ($message !== ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                required
            >

        </div>


        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
            >

        </div>


        <button
            type="submit"
            class="auth-btn"
        >
            Login
        </button>

    </form>


    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Create Account
        </a>

    </div>

</div>


<footer>

    <p>© 2026 FreshCart - Online Grocery Shop</p>

</footer>

</body>

</html>