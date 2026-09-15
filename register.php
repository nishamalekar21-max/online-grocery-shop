<?php

session_start();
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all required fields.";
    
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {

        $message = "Name can contain only alphabets and spaces.";
    
    } elseif (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password) ||
        !preg_match("/[^A-Za-z0-9]/", $password)
    ) {
    
        $message = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    
    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        
        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            // Never store the password directly.
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, phone, address)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $hashed_password,
                $phone,
                $address
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed.";
            }
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

    <title>Register - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .auth-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
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
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group textarea {
            height: 90px;
            resize: vertical;
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
            background: #e8f5e9;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
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
        <a href="login.php">Login</a>
    </nav>

</header>


<div class="auth-container">

    <h1>Create Account</h1>

    <?php if ($message !== ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>Full Name</label>

            <input
    type="text"
    name="name"
    pattern="[A-Za-z ]+"
    title="Name can contain only alphabets and spaces"
    placeholder="Enter your full name"
    required
>
        </div>


        <div class="form-group">

            <label>Email</label>

            <input
    type="email"
    name="email"
    placeholder="Enter your email"
    required
>

        <div class="form-group">

            <label>Phone</label>

            <input
                type="text"
                name="phone"
            >

        </div>


        <div class="form-group">

            <label>Address</label>

            <textarea
                name="address"
                placeholder="Enter your delivery address"
            ></textarea>

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
            Create Account
        </button>

    </form>


    <div class="login-link">

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </div>

</div>


<footer>

    <p>© 2026 FreshCart - Online Grocery Shop</p>

</footer>

</body>

</html>