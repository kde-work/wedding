<?php

/**
 * Bambora Online Checkout Customer
 */
class Payment_Customer {
    /** @var string */
    public $email;
    /** @var string */
    public $phonenumber;
    /** @var string */
    public $phonenumbercountrycode;
}

/**
 * Bambora Online Checkout Order
 */
class Payment_Order {

    /** @var Payment_Address */
    public $billingaddress;
    /** @var string */
    public $currency;
    /** @var Payment_Orderline[] */
    public $lines;
    /** @var string */
    public $ordernumber;
    /** @var Payment_Address */
    public $shippingaddress;
    /** @var long */
    public $total;
    /** @var long */
    public $vatamount;
}

/**
 * Bambora Online Checkout Address
 */
class Payment_Address {
    /** @var string */
    public $att;
    /** @var string */
    public $city;
    /** @var string */
    public $country;
    /** @var string */
    public $firstname;
    /** @var string */
    public $lastname;
    /** @var string */
    public $street;
    /** @var string */
    public $zip;
}

/**
 * Bambora Online Checkout Orderline
 */
class Payment_Orderline {
    /** @var string */
    public $description;
    /** @var string */
    public $id;
    /** @var string */
    public $linenumber;
    /** @var float */
    public $quantity;
    /** @var string */
    public $text;
    /** @var int|long */
    public $totalprice;
    /** @var int|long */
    public $totalpriceinclvat;
    /** @var int|long */
    public $totalpricevatamount;
    /** @var string */
    public $unit;
    /** @var int|long */
    public $vat;
}

/**
 * Bambora Online Checkout Url
 */
class Payment_Url {
    /** @var string */
    public $accept;
    /** @var  Payment_Callback[]  */
    public $callbacks;
    /** @var string */
    public $decline;
}

/**
 * Bambora Online Checkout Callback
 */
class Payment_Callback {
    /** @var string */
    public $url;
}



/**
 * Bambora Online Checkout Request
 */
class Payment_Subscription {
    /** @var string */
    public $action;
    /** @var string */
    public $decription;
    /** @var string */
    public $reference;
}

/**
 * Bambora Online Checkout Request
 */
class Payment_Request {

    /** @var Payment_Customer */
    public $customer;
    /** @var long */
    public $instantcaptureamount;
    /** @var string */
    public $language;
    /** @var Payment_Order */
    public $order;
    /** @var Payment_Subscription */
    public $subscription;
    /** @var Payment_Url */
    public $url;
    /** @var int */
    public $paymentwindowid;
}
