<?php

function getEsewaConfig(): array
{
    $merchantCode = getenv("ESEWA_MERCHANT_CODE") ?: ($_ENV["ESEWA_MERCHANT_CODE"] ?? "EPAYTEST");
    $secretKey = getenv("ESEWA_SECRET_KEY") ?: ($_ENV["ESEWA_SECRET_KEY"] ?? "test_secret_key");
    $baseUrl = getenv("ESEWA_BASE_URL") ?: ($_ENV["ESEWA_BASE_URL"] ?? "https://rc-epay.esewa.com.np/api/epay/main/v2/payment");
    $appBaseUrl = getenv("APP_BASE_URL") ?: ($_ENV["APP_BASE_URL"] ?? "http://localhost/online-grocery-shop");

    return [
        "merchant_code" => $merchantCode,
        "secret_key" => $secretKey,
        "base_url" => $baseUrl,
        "app_base_url" => rtrim($appBaseUrl, "/"),
    ];
}

function getEsewaCallbackUrls(int $orderId): array
{
    $config = getEsewaConfig();

    return [
        "success" => $config["app_base_url"] . "/payment.php?order_id=" . (int) $orderId . "&status=Success",
        "failure" => $config["app_base_url"] . "/payment.php?order_id=" . (int) $orderId . "&status=Failure",
    ];
}

function isEsewaConfigured(): bool
{
    $config = getEsewaConfig();

    return (
        !empty($config["merchant_code"]) &&
        $config["merchant_code"] !== "EPAYTEST" &&
        !empty($config["secret_key"]) &&
        $config["secret_key"] !== "test_secret_key"
    );
}

function generateEsewaSignature(array $data, string $secretKey): string
{
    ksort($data);
    $message = "";

    foreach ($data as $key => $value) {
        if ($value !== "") {
            $message .= $key . "=" . $value . "";
        }
    }

    return hash_hmac("sha256", $message, $secretKey);
}

function verifyEsewaSignature(array $data, string $secretKey): bool
{
    if (!isset($data["signature"])) {
        return false;
    }

    $signature = $data["signature"];
    unset($data["signature"]);

    return hash_equals(generateEsewaSignature($data, $secretKey), $signature);
}
