<?php

session_start();

require_once "config/database.php";
require_once "config/payment.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET["order_id"] ?? 0);

if ($order_id <= 0) {
    header("Location: products.php");
    exit;
}

// Get order
$stmt = $conn->prepare(
    "SELECT id, total_amount, status
     FROM orders
     WHERE id = ? AND user_id = ?"
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

$message = "";
$esewaEnabled = isEsewaConfigured();
$esewaFormData = null;

if (isset($_GET["status"], $_GET["oid"], $_GET["amt"], $_GET["refId"])) {
    $status = $_GET["status"] ?? "";
    $oid = $_GET["oid"] ?? "";
    $amount = $_GET["amt"] ?? "0";
    $refId = $_GET["refId"] ?? "";

    if ($status === "Success") {
        $update = $conn->prepare(
            "UPDATE payments
             SET payment_method = 'eSewa',
                 transaction_id = ?,
                 payment_status = 'Paid',
                 paid_at = NOW()
             WHERE order_id = ?"
        );

        $update->bind_param("si", $refId, $order_id);

        if ($update->execute()) {
            $order_update = $conn->prepare(
                "UPDATE orders
                 SET status = 'Confirmed'
                 WHERE id = ?"
            );

            $order_update->bind_param("i", $order_id);
            $order_update->execute();

            header("Location: order-success.php?order_id=" . $order_id);
            exit;
        }

        $message = "eSewa payment verification failed. Please contact support.";
    } else {
        $message = "Your eSewa payment was not completed.";
    }
}

// Payment submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $payment_method = $_POST["payment_method"] ?? "";

    $allowed_methods = [
        "Demo Card",
        "Cash on Delivery",
        "eSewa"
    ];

    if (!in_array($payment_method, $allowed_methods, true)) {
        $message = "Please select a valid payment method.";
    } else {
        if ($payment_method === "Demo Card") {
            $transaction_id = "DEMO-" . strtoupper(bin2hex(random_bytes(5)));
            $payment_status = "Paid";
            $paid_at = date("Y-m-d H:i:s");
        } elseif ($payment_method === "Cash on Delivery") {
            $transaction_id = null;
            $payment_status = "Pending";
            $paid_at = null;
        } else {
            if (!$esewaEnabled) {
                $message = "eSewa is not configured yet. Add your merchant settings in the server environment.";
            } else {
                $config = getEsewaConfig();
                $esewaFormData = [
                    "amount" => number_format((float) $order["total_amount"], 2, '.', ''),
                    "pid" => "ORDER-" . $order_id,
                    "scd" => $config["merchant_code"],
                ];

                $update = $conn->prepare(
                    "UPDATE payments
                     SET payment_method = ?,
                         transaction_id = NULL,
                         payment_status = 'Pending',
                         paid_at = NULL
                     WHERE order_id = ?"
                );

                $update->bind_param("si", $payment_method, $order_id);

                if (!$update->execute()) {
                    $message = "Payment could not be processed.";
                }
            }
        }

        if ($payment_method !== "eSewa" && $message === "") {
            $update = $conn->prepare(
                "UPDATE payments
                 SET payment_method = ?,
                     transaction_id = ?,
                     payment_status = ?,
                     paid_at = ?
                 WHERE order_id = ?"
            );

            $update->bind_param(
                "ssssi",
                $payment_method,
                $transaction_id,
                $payment_status,
                $paid_at,
                $order_id
            );

            if ($update->execute()) {
                if ($payment_status === "Paid") {
                    $order_update = $conn->prepare(
                        "UPDATE orders
                         SET status = 'Confirmed'
                         WHERE id = ?"
                    );

                    $order_update->bind_param("i", $order_id);
                    $order_update->execute();
                }

                header("Location: order-success.php?order_id=" . $order_id);
                exit;
            }

            $message = "Payment could not be processed.";
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

    <title>Payment - FreshCart</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .payment-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .payment-header h1 {
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .payment-header p {
            font-size: 15px;
            color: #666;
        }

        .payment-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
        }

        .order-summary {
            background: #ffffff;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .summary-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
            color: #555;
            border-bottom: 1px solid #f5f5f5;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item strong {
            color: #1a1a1a;
        }

        .order-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: #2ca84a;
        }

        .payment-methods {
            background: #ffffff;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            display: flex;
            flex-direction: column;
        }

        .methods-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .payment-option {
            display: block;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option input[type="radio"] + label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .payment-option input[type="radio"]:checked + label {
            background: #f0f8f5;
            border-color: #2ca84a;
        }

        .payment-option label::before {
            content: '';
            display: block;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-radius: 50%;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .payment-option input[type="radio"]:checked + label::before {
            background: #2ca84a;
            border-color: #2ca84a;
            box-shadow: inset 0 0 0 4px #ffffff;
        }

        .payment-option label span {
            color: #1a1a1a;
            font-weight: 600;
            font-size: 15px;
        }

        .payment-option label small {
            position: absolute;
            font-size: 12px;
            color: #999;
            margin-top: 26px;
            margin-left: 32px;
        }

        .submit-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2ca84a 0%, #249a41 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(44, 168, 74, 0.3);
        }

        .cancel-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #f5f5f5;
            color: #666;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background: #efefef;
            color: #333;
        }

        .message {
            padding: 14px 16px;
            margin-bottom: 20px;
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
            border-radius: 8px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .payment-wrapper {
                grid-template-columns: 1fr;
            }

            .payment-header h1 {
                font-size: 24px;
            }
        }

    </style>

</head>

<body>

<div class="payment-container">
    <div class="payment-header">
        <h1>Secure Checkout</h1>
        <p>Complete your order by selecting a payment method</p>
    </div>

    <div class="payment-wrapper">
        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-title">Order Summary</div>
            
            <div class="order-item">
                <span>Order ID:</span>
                <strong>#<?php echo $order["id"]; ?></strong>
            </div>

            <div class="order-item">
                <span>Subtotal:</span>
                <strong>Rs. <?php echo number_format($order["total_amount"], 2); ?></strong>
            </div>

            <div class="order-item">
                <span>Delivery:</span>
                <strong>Free</strong>
            </div>

            <div class="order-item">
                <span>Tax:</span>
                <strong>Rs. 0.00</strong>
            </div>

            <div class="order-total">
                <span class="total-label">Total Amount</span>
                <span class="total-amount">Rs. <?php echo number_format($order["total_amount"], 2); ?></span>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="payment-methods">
            <div class="methods-title">Payment Method</div>

            <?php if ($message !== ""): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" id="payment-form">
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="demo-card" name="payment_method" value="Demo Card" required>
                        <label for="demo-card">
                            <span>💳 Demo Card</span>
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="cod" name="payment_method" value="Cash on Delivery" required>
                        <label for="cod">
                            <span>📦 Cash on Delivery</span>
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="esewa" name="payment_method" value="eSewa" required>
                        <label for="esewa">
                            <span>🏧 eSewa Payment</span>
                        </label>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="submit-btn">Proceed to Payment</button>
                    <a href="cart.php" class="cancel-btn">← Back to Cart</a>
                </div>
            </form>

            <script>
                // Make labels clickable for radio buttons
                document.querySelectorAll('.payment-option label').forEach(label => {
                    label.addEventListener('click', function(e) {
                        const radio = this.previousElementSibling;
                        if (radio && radio.type === 'radio') {
                            radio.checked = true;
                            radio.focus();
                        }
                    });
                });

                // Handle form submission
                document.getElementById('payment-form').addEventListener('submit', function(e) {
                    const selected = document.querySelector('input[name="payment_method"]:checked');
                    if (!selected) {
                        e.preventDefault();
                        alert('Please select a payment method');
                    }
                });
            </script>
        </div>
    </div>
</div>

<?php if ($esewaEnabled && $esewaFormData !== null): ?>
    <?php $callbackUrls = getEsewaCallbackUrls((int) $order_id); ?>
    <form id="esewa-form" method="POST" action="<?php echo htmlspecialchars(getEsewaConfig()["base_url"] ?? "https://rc-epay.esewa.com.np/api/epay/main/v2/payment", ENT_QUOTES); ?>">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($esewaFormData["amount"], ENT_QUOTES); ?>">
        <input type="hidden" name="pid" value="<?php echo htmlspecialchars($esewaFormData["pid"], ENT_QUOTES); ?>">
        <input type="hidden" name="scd" value="<?php echo htmlspecialchars($esewaFormData["scd"], ENT_QUOTES); ?>">
        <input type="hidden" name="su" value="<?php echo htmlspecialchars($callbackUrls["success"], ENT_QUOTES); ?>">
        <input type="hidden" name="fu" value="<?php echo htmlspecialchars($callbackUrls["failure"], ENT_QUOTES); ?>">
    </form>
    <script>
        document.getElementById('esewa-form').submit();
    </script>
<?php endif; ?>

</body>

</html>