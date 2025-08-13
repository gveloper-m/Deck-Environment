<?php

namespace App\Services;

use GuzzleHttp\Client;

class PayPalService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $secret;

    public function __construct()
    {
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->secret   = env('PAYPAL_CLIENT_SECRET');
        $mode           = env('PAYPAL_MODE', 'sandbox');

        $this->baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->client = new Client();
    }

    public function getAccessToken()
    {
        $response = $this->client->post("{$this->baseUrl}/v1/oauth2/token", [
            'auth' => [$this->clientId, $this->secret],
            'form_params' => ['grant_type' => 'client_credentials'],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    public function createOrder($amount, $currency = 'USD', $returnUrl = '', $cancelUrl = '')
    {
        $token = $this->getAccessToken();

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'return_url'    => $returnUrl,
                    'cancel_url'    => $cancelUrl,
                    'landing_page'  => 'BILLING',  // Show card form first
                    'user_action'   => 'PAY_NOW',  // Emphasize immediate payment
                    'brand_name'    => config('app.name', 'My App'), // Optional: your brand
                ]
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
