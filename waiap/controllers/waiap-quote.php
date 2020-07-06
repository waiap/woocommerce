<?php
require_once WC_ABSPATH . 'includes/wc-cart-functions.php';

class WC_Waiap_Quote
{

  protected $environment;

  public function __construct(){
    $this->waiap_settings = get_option('woocommerce_waiap_woocommerce_settings');
    $this->environment    = $this->waiap_settings["waiap_environment"];
    $this->checkout_helper = new WC_Waiap_Checkout_Helper();
  }

  public function getQuoteInfo(){
    $quoteData = [];
    if(WC()->cart === null){
        $this->checkout_helper->loadFromSession();
    }
    $quote = WC()->cart;

    $quoteAmount  = $quote ? $quote->total : "0";

    //$customerId   = WC()->customer ? WC()->customer->id : "0";

    $quoteData["groupId"]     = "0";
    $quoteData["amount"]      = $quoteAmount;
    $quoteData["currency"]    = get_woocommerce_currency();

    return $quoteData;
  }
}
