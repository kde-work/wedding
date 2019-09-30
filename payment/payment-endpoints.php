<?php
define( 'PAYMENT_ENDPOINT_TRANSACTION', 'https://transaction-v1.api-eu.bambora.com' );
define( 'PAYMENT_ENDPOINT_MERCHANT', 'https://merchant-v1.api-eu.bambora.com' );
define( 'PAYMENT_ENDPOINT_DATA', 'https://data-v1.api-eu.bambora.com' );
define( 'PAYMENT_ENDPOINT_SUBSCRIPTION', 'https://subscription-v1.api-eu.bambora.com' );
define( 'PAYMENT_ENDPOINT_CHECKOUT', 'https://v1.checkout.bambora.com');
define( 'PAYMENT_ENDPOINT_CHECKOUT_API', 'https://api.v1.checkout.bambora.com' );
define( 'PAYMENT_CHECKOUT_ASSETS', 'https://v1.checkout.bambora.com/Assets' );

/**
 * Payment Endpoints
 */
class Payment_Endpoints {

    /**
     * Get Transaction Endpoint
     *
     * @return mixed
     */
    public static function get_transaction_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_TRANSACTION' );
    }

    /**
     * Get Merchant Endpoint
     *
     * @return mixed
     */
    public static function get_merchant_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_MERCHANT' );
    }

    /**
     * Get Data Endpoint
     *
     * @return mixed
     */
    public static function get_data_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_DATA' );
    }

    /**
     * Get Subscription Endpoint
     *
     * @return mixed
     */
    public static function get_subscription_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_SUBSCRIPTION' );
    }

    /**
     * Get Checkout Endpoint
     *
     * @return mixed
     */
    public static function get_checkout_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_CHECKOUT' );
    }

    /**
     * Get Checkout Endpoint
     *
     * @return mixed
     */
    public static function get_checkout_api_endpoint() {
        return constant( 'PAYMENT_ENDPOINT_CHECKOUT_API' );
    }

    /**
     * Get Assets Endpoint
     *
     * @return mixed
     */
    public static function get_checkout_assets() {
        return constant( 'PAYMENT_CHECKOUT_ASSETS' );
    }
}
