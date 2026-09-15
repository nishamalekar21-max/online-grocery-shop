<?php

/**
 * eSewa Merchant Credentials Configuration
 * 
 * Update these values with your actual eSewa merchant account details
 * Get your credentials from: https://dashboard.esewa.com.np/
 */

// ========== eSewa Configuration ==========

// Merchant Code (from your eSewa dashboard)
// For testing: Use "EPAYTEST" (provided by eSewa for testing)
// For production: Use your actual merchant code
putenv("ESEWA_MERCHANT_CODE=EPAYTEST");

// Secret Key (from your eSewa dashboard)
// For testing: Use "test_secret_key" 
// For production: Use your actual secret key
putenv("ESEWA_SECRET_KEY=test_secret_key");

// eSewa API Base URL
// Testing: https://rc-epay.esewa.com.np/api/epay/main/v2/payment
// Production: https://epay.esewa.com.np/api/epay/main/v2/payment
putenv("ESEWA_BASE_URL=https://rc-epay.esewa.com.np/api/epay/main/v2/payment");

// ========== Application URL Configuration ==========

// Your application's base URL
// For localhost: http://localhost/online-grocery-shop
// For production: https://yourdomain.com/online-grocery-shop
putenv("APP_BASE_URL=http://localhost/online-grocery-shop");

// ========== Instructions ==========
// 
// To use real eSewa payments:
// 1. Register at: https://esewa.com.np/
// 2. Complete merchant verification
// 3. Login to: https://dashboard.esewa.com.np/
// 4. Get your merchant code and secret key
// 5. Replace the values above with your credentials
// 6. Update ESEWA_BASE_URL to production URL
// 7. Update APP_BASE_URL to your public domain (important for callbacks)
//
// For localhost testing:
// - Use the EPAYTEST credentials provided above
// - eSewa callbacks won't work on localhost
// - Use ngrok (https://ngrok.com/) to get a public URL:
//   ngrok http 80
// - Then update APP_BASE_URL with ngrok URL
// ========================================
