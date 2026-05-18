<?php
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';
require_once __DIR__ . '/../classes/PayPalGateway.php';
require_once __DIR__ . '/../classes/ManualGateway.php';
require_once __DIR__ . '/../classes/StripeGateway.php';

/**
 * Factory for instantiating payment gateways by name.
 */
class GatewayFactory
{
    /**
     * @param string $name  Gateway name: 'paypal', 'stripe', 'manual'
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public static function make(string $name): PaymentGatewayInterface
    {
        switch (strtolower($name)) {
            case 'paypal':
                return PayPalGateway::fromConfig();
            case 'manual':
                return new ManualGateway();
            case 'stripe':
                return new StripeGateway();
            default:
                throw new InvalidArgumentException("Unknown payment gateway: {$name}");
        }
    }
}
