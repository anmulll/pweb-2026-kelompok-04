<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Create payment session / checkout url with the gateway.
     *
     * @return array{gateway_checkout_url: ?string, gateway_token: ?string, expired_at: ?string, raw_response: array}
     */
    public function createPayment(Order $order): array;

    /**
     * Check status of payment directly from gateway.
     *
     * @return array
     */
    public function checkStatus(Order $order): array;
}
