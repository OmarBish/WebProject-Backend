<?php

namespace App;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

class PayPalClient
{
    /**
     * Returns PayPal HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PayPal APIs, provided the
     * credentials have access.
     */
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use ProductionEnvironment.
     */
    public static function environment()
    {
        $clientId = env('PAYPAL_CLIENT_ID', 'AUyDev7cz_px2_6PD5X94J1xj7-psIWIN6TYSJMmUNGI7FsuPyrjGRtfSv6jsDp926SODr65Li768z4b');
        $clientSecret = env('PAYPAL_CLIENT_ID', 'EABqnH4WOu0-JbRHmPNVViK2rgUTcWBv4bjWDE5qYjC58uUmsQZYO6zWkFdAXXa3JIdZdX_uocdjooLO');
        return new SandboxEnvironment($clientId, $clientSecret);
    }
}