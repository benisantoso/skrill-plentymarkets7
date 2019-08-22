<?php
namespace Skrill\Configs;

interface MethodConfigContract
{
    
    /**
     * Returns the available payment methods and their helper strings (config-key, payment-key, default name).
     *
     * @return string[]
     */
    public static function getPaymentMethods(): array;

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentMethodDefaultName(string $paymentMethod): string;
    
    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentMethodKey(string $paymentMethod): string;
}
