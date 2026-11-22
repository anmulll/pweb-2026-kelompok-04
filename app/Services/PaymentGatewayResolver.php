<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Services\Doku\DokuCheckoutGateway;
use InvalidArgumentException;

class PaymentGatewayResolver
{
    public function __construct(
        protected DokuCheckoutGateway $dokuCheckoutGateway
    ) {}

    public function resolve(string|Payment $providerOrPayment): PaymentGatewayInterface
    {
        $provider = $providerOrPayment instanceof Payment
            ? $providerOrPayment->provider
            : $providerOrPayment;

        return match ($provider) {
            'doku_sandbox' => $this->dokuCheckoutGateway,
            default => throw new InvalidArgumentException("Unsupported payment gateway provider: {$provider}"),
        };
    }
}
