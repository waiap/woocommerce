<?php

require_once __ROOT__.'/helpers/index.php';
require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
include_once WC_ABSPATH . 'includes/wc-notice-functions.php';


class WC_Waiap_Checkout_Helper
{

  CONST EXCLUDED_SUSPECTED_FRAUD_METHODS = ["azon", "altp_bizum", "altp_bankia_transfer", "altp_bankia"];

  public function placeOrderFromResponse($requestJSON, $response, $method){
    $responseJSON = json_decode(json_encode($response),true);
    // add payment method and convert quote to order
    return $this->convertQuoteToOrder($requestJSON,$responseJSON, $method);
  }
  
  public function convertQuoteToOrder($request, $response, $method){
    WC_Waiap_Payment_Log::log("CONVERT TO QUOTE CUSTOMER");
    if(WC()->customer === null){
      $this->loadFromSession();
    }

    $checkoutData = WC()->session->get("waiap_session");
    //$checkoutData = $_COOKIE["waiap_session"];
    WC_Waiap_Payment_Log::log("SESSION_DATA    " . base64_decode($checkoutData));
    $arrayData    = json_decode(base64_decode($checkoutData), true);
    WC()->session->set("chosen_shipping_methods",$arrayData["shipping_method"]);

    $data = array(
      'terms'                              => (int) "0",
			'createaccount'                      => (int) "0",
			'payment_method'                     => "waiap_woocommerce",
			'shipping_method'                    =>  WC()->session->get("chosen_shipping_methods"),
			'ship_to_different_address'          => $arrayData !== null && array_key_exists("ship_to_different_address", $arrayData) ? $arrayData["ship_to_different_address"] : false,
			'woocommerce_checkout_update_totals' => false,
      'order_comments'                     => $arrayData["order_comments"],
      'shipping_first_name' => WC()->customer->get_shipping_first_name('edit') == "" || WC()->customer->get_shipping_first_name('edit') == null
                                ? $arrayData["shipping_first_name"] == ""
                                  ? $arrayData["billing_first_name"]  : $arrayData["shipping_first_name"]
                                : WC()->customer->get_shipping_first_name('edit'),
      'shipping_last_name'  => WC()->customer->get_shipping_last_name('edit')  == "" || WC()->customer->get_shipping_last_name('edit')  == null
                                ? $arrayData["shipping_last_name"] == ""
                                  ? $arrayData["billing_last_name"]  : $arrayData["shipping_last_name"]
                                : WC()->customer->get_shipping_last_name('edit'),
      'shipping_company'    => WC()->customer->get_shipping_company('edit')    == "" || WC()->customer->get_shipping_company('edit')    == null
                                ? $arrayData["shipping_company"] == ""
                                  ? $arrayData["billing_company"]  : $arrayData["shipping_company"]
                                : WC()->customer->get_shipping_first_name('edit'),
      'shipping_address_1'  => WC()->customer->get_shipping_address_1('edit')  == "" || WC()->customer->get_shipping_address_1('edit')  == null
                                ? $arrayData["shipping_address_1"] == ""
                                  ? $arrayData["billing_address_1"]  : $arrayData["shipping_address_1"]
                                : WC()->customer->get_shipping_address_1('edit'),
      'shipping_address_2'  => WC()->customer->get_shipping_address_2('edit')  == "" || WC()->customer->get_shipping_address_2('edit')  == null
                                ? $arrayData["shipping_address_2"] == ""
                                  ? $arrayData["billing_address_2"]  : $arrayData["shipping_address_2"]
                                : WC()->customer->get_shipping_address_2('edit'),
      'shipping_city'       => WC()->customer->get_shipping_city('edit')       == "" || WC()->customer->get_shipping_city('edit')       == null
                                ? $arrayData["shipping_city"] == ""
                                  ? $arrayData["billing_city"]  : $arrayData["shipping_city"]
                                : WC()->customer->get_shipping_city('edit'),
      'shipping_state'      => WC()->customer->get_shipping_state('edit')      == "" || WC()->customer->get_shipping_state('edit')      == null
                                ? $arrayData["shipping_state"] == ""
                                  ? $arrayData["billing_state"]  : $arrayData["shipping_state"]
                                : WC()->customer->get_shipping_state('edit'),
      'shipping_postcode'   => WC()->customer->get_shipping_postcode('edit')   == "" || WC()->customer->get_shipping_postcode('edit')   == null
                                ? $arrayData["shipping_postcode"] == ""
                                  ? $arrayData["billing_postcode"]  : $arrayData["shipping_postcode"]
                                : WC()->customer->get_shipping_postcode('edit'),
      'shipping_country'    => WC()->customer->get_shipping_country('edit')    == "" || WC()->customer->get_shipping_country('edit')    == null
                                ? $arrayData["shipping_country"] == ""
                                  ? $arrayData["billing_country"]  : $arrayData["shipping_country"]
                                : WC()->customer->get_shipping_country('edit'),
      'billing_first_name'  => WC()->customer->get_billing_first_name('edit') == "" || WC()->customer->get_billing_first_name('edit') == null
                                ? $arrayData["billing_first_name"] : WC()->customer->get_billing_first_name('edit'),
      'billing_last_name'   => WC()->customer->get_billing_last_name('edit')  == "" || WC()->customer->get_billing_last_name('edit')  == null
                                ? $arrayData["billing_first_name"] : WC()->customer->get_billing_last_name('edit'),
      'billing_company'     => WC()->customer->get_billing_company('edit')    == "" || WC()->customer->get_billing_company('edit')    == null
                                ? $arrayData["billing_company"] : WC()->customer->get_billing_company('edit'),
      'billing_address_1'   => WC()->customer->get_billing_address_1('edit')  == "" || WC()->customer->get_billing_address_1('edit')  == null
                                ? $arrayData["billing_address_1"] : WC()->customer->get_billing_address_1('edit'),
      'billing_address_2'   => WC()->customer->get_billing_address_2('edit')  == "" || WC()->customer->get_billing_address_2('edit')  == null
                                ? $arrayData["billing_address_2"] : WC()->customer->get_billing_address_2('edit'),
      'billing_city'        => WC()->customer->get_billing_city('edit')       == "" || WC()->customer->get_billing_city('edit')       == null
                                ? $arrayData["billing_city"] : WC()->customer->get_billing_city('edit') ,
      'billing_state'       => WC()->customer->get_billing_state('edit')      == "" || WC()->customer->get_billing_state('edit')      == null
                                ? $arrayData["billing_state"] : WC()->customer->get_billing_state('edit'),
      'billing_postcode'    => WC()->customer->get_billing_postcode('edit')   == "" || WC()->customer->get_billing_postcode('edit')   == null
                                ? $arrayData["billing_postcode"] : WC()->customer->get_billing_postcode('edit'),
      'billing_country'     => WC()->customer->get_billing_country('edit')    == "" || WC()->customer->get_billing_country('edit')    == null
                                ? $arrayData["billing_country"] : WC()->customer->get_billing_country('edit'),
      'billing_email'       => WC()->customer->get_billing_email('edit')      == "" || WC()->customer->get_billing_email('edit')      == null
                                ? $arrayData["billing_email"] : WC()->customer->get_billing_email('edit'),
      'billing_phone'       => WC()->customer->get_billing_phone('edit')      == "" || WC()->customer->get_billing_phone('edit')      == null
                                ? $arrayData["billing_phone"] : WC()->customer->get_billing_phone('edit')
    );
    define( 'WOOCOMMERCE_CHECKOUT', true );
    define( 'WOOCOMMERCE_CART', true );
    WC()->cart->calculate_totals();
    WC()->cart->calculate_shipping();

    $order_id = WC()->checkout->create_order($data);

    if($order_id !== null){
      $order = wc_get_order($order_id );
      wc_reduce_stock_levels($order->get_id());
      // if(!in_array($method, self::EXCLUDED_SUSPECTED_FRAUD_METHODS)){
      //   $this->detectSuspectedFraud($response, $order);
      // }else{
        $order->update_status("processing", 'Order processed by Waiap PaymentWall', TRUE);
      //}
      WC()->cart->empty_cart(true);
      WC()->session->set('cart', array());
    }

    WC_Waiap_Payment_Log::log("CONVERTED QUOTE TO ORDER ". json_encode($order_id));
    return $order_id;
  }

  private function detectSuspectedFraud($responseJSON, &$order){
    $responseAmount = 0;
    WC_Waiap_Payment_Log::log(json_encode($responseJSON));
    $flattenResponse = $this->flatten($responseJSON);
    WC_Waiap_Payment_Log::log(json_encode($flattenResponse));
    foreach ($flattenResponse as $key => $value) {
      if (strpos($key, 'amount') !== false){
        $responseAmount = $value;
        break;
      }
    }
    WC_Waiap_Payment_Log::log($responseAmount);
    if((floatval($responseAmount)/100) != $order->get_total()){
      WC_Waiap_Payment_Log::log("SUSPECTED FROUD DETECTED");
      if($order != null){
        $order->update_status("on-hold", 'Suspected fraud, captured '. floatval($responseAmount/100) .' but order value is '. $order->get_total(), TRUE);
      }
    }else{
      $order->update_status("processing", 'Order processed by Waiap PaymentWall', TRUE);
    }
  }

  private function flatten($array, $prefix = '') {
    $result = array();
    foreach($array as $key=>$value) {
        if(is_array($value)) {
            $result = $result + $this->flatten($value, $prefix . $key . '.');
        }
        else {
            $result[$prefix . $key] = $value;
        }
    }
    return $result;
  }

  public function loadFromSession(){
    if(WC()->session === null) {
      $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
      WC()->session = new $session_class();
      WC()->session->init();
    }

    if(WC()->cart === null) {
      WC()->cart = new WC_Cart();
    }
    //
    if(WC()->customer === null) {
      WC()->customer = new WC_Customer( WC()->session->get_customer_id(), true );
    }

    WC()->cart->get_cart();

    //WC()->checkout->update_session();
  }

}
