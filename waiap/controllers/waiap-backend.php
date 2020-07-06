<?php

class WC_Waiap_Backend
{

protected $key;
protected $resource;
protected $secret;
protected $environment;
protected $proxy_helper;

public function __construct(){
  $this->waiap_settings = get_option('woocommerce_waiap_woocommerce_settings');
  $this->environment    = $this->waiap_settings["waiap_environment"];
  $this->key            = $this->waiap_settings["waiap_key"];
  $this->resource       = $this->waiap_settings["waiap_resource"];
  $this->secret         = $this->waiap_settings["waiap_secret"];
  
  $this->checkout_helper = new WC_Waiap_Checkout_Helper();

}

public function addOrderInfo(&$pwall_request){
  if(WC()->cart === null){
    $this->checkout_helper->loadFromSession();
  }
  $quote = WC()->cart;

  $pwall_request->setOrderId($quote == null ? "000000" : strval((int)(microtime(true)*100)));
  $pwall_request->setAmount($quote == null ? "0" : floatval($quote->total));
  $pwall_request->setCurrency(get_woocommerce_currency());
  $pwall_request->setGroupId(strval(WC()->customer->get_id()));
  $pwall_request->setOriginalUrl(get_bloginfo('url'));
}

/**
 * Process payment
 *
 * @param array $request Request object
 *
 * @return array
 */
public function actions($request){
    $this->client           = new \PWall\Client();
    $jsonRequest  = $request->get_json_params();
    WC_Waiap_Payment_Log::log("ON BACKEND EXECUTE: " . json_encode($jsonRequest));

    $this->client->setEnvironment($this->environment);
    $this->client->setKey($this->key);
    $this->client->setResource($this->resource);
    $this->client->setSecret($this->secret);
    $this->client->setBackendUrl(wc_get_checkout_url());

    if (WC()->cart === null) {
      $this->checkout_helper->loadFromSession();
    }
    $quote = WC()->cart;
    if($quote->get_cart_contents_count() == 0){
      $request = new \PWall\Request(json_encode($jsonRequest), true);
    }else{
      $request = new \PWall\Request(json_encode($jsonRequest), false);
      $this->addOrderInfo($request);
    }

    $response = $this->client->proxy($request);

    if ($response->canPlaceOrder()) {
      $orderId = $this->checkout_helper->placeOrderFromResponse($jsonRequest, $response, $jsonRequest["params"]["method"]);
      $payment_gateway = new WC_Waiap_Paymentwall();
      WC_Waiap_Payment_Log::log("ACTION: setCookieToRedirect");
      setcookie("success_redirect", $payment_gateway->get_return_url(wc_get_order($orderId)), time() + 10, "/");
    }

    return json_decode($response->toJSON());
  }
}
