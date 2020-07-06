<?php

class WC_Waiap_Checkout
{

  public function __construct(){

  }

  public function setCheckoutInfo($request){
    $checkoutData = $request->get_params();
    WC()->session->set("waiap_session", base64_encode(json_encode($checkoutData)));

    if (WC()->customer === null) {
      WC()->customer = new WC_Customer(WC()->session->get_customer_id(), true);
    }

    WC()->customer->set_first_name($checkoutData["shipping_first_name"] == ""
      ? $checkoutData["billing_first_name"]  : $checkoutData["shipping_first_name"]);

    WC()->customer->set_last_name($checkoutData["shipping_last_name"] == ""
      ? $checkoutData["billing_last_name"]  : $checkoutData["shipping_last_name"]);

    //SHIPPING ADDRESS
    WC()->session->set("chosen_shipping_methods", $checkoutData["shipping_method"]);

    WC()->customer->set_shipping_first_name($checkoutData["shipping_first_name"] == ""
                                ? $checkoutData["billing_first_name"]  : $checkoutData["shipping_first_name"]);
    WC()->customer->set_shipping_last_name($checkoutData["shipping_last_name"] == ""
                                ? $checkoutData["billing_last_name"]  : $checkoutData["shipping_last_name"]);
    WC()->customer->set_shipping_company($checkoutData["shipping_company"] == ""
                                ? $checkoutData["billing_company"]  : $checkoutData["shipping_company"]);
    WC()->customer->set_shipping_address_1($checkoutData["shipping_address_1"] == ""
                                ? $checkoutData["billing_address_1"]  : $checkoutData["shipping_address_1"]);
    WC()->customer->set_shipping_address_2($checkoutData["shipping_address_2"] == ""
                                ? $checkoutData["billing_address_2"]  : $checkoutData["shipping_address_2"]);
    WC()->customer->set_shipping_city($checkoutData["shipping_city"] == ""
                                ? $checkoutData["billing_city"]  : $checkoutData["shipping_city"]);
    WC()->customer->set_shipping_state($checkoutData["shipping_state"] == ""
                                ? $checkoutData["billing_state"]  : $checkoutData["shipping_state"]);
    WC()->customer->set_shipping_postcode($checkoutData["shipping_postcode"] == ""
                                ? $checkoutData["billing_postcode"]  : $checkoutData["shipping_postcode"]);
    WC()->customer->set_shipping_country($checkoutData["shipping_country"] == ""
                                ? $checkoutData["billing_country"]  : $checkoutData["shipping_country"]);
    //BILLING ADDRESS
    WC()->customer->set_billing_first_name($checkoutData["billing_first_name"]);
    WC()->customer->set_billing_last_name($checkoutData["billing_first_name"]);
    WC()->customer->set_billing_company($checkoutData["billing_company"]);
    WC()->customer->set_billing_address_1($checkoutData["billing_address_1"]);
    WC()->customer->set_billing_address_2($checkoutData["billing_address_2"]);
    WC()->customer->set_billing_city($checkoutData["billing_city"]);
    WC()->customer->set_billing_state($checkoutData["billing_state"]);
    WC()->customer->set_billing_postcode($checkoutData["billing_postcode"]);
    WC()->customer->set_billing_country($checkoutData["billing_country"]);
    WC()->customer->set_billing_email($checkoutData["billing_email"]);
    WC()->customer->set_billing_phone($checkoutData["billing_phone"]);

    WC()->session->set( 'chosen_payment_method', "waiap_woocommerce" );
    //setcookie("waiap_session", base64_encode(json_encode($checkoutData)));
    return $request;
  }

  public function getCheckoutInfo($request){
    $checkoutData = WC()->session->get("waiap_session");
    return base64_decode($checkoutData);
  }
}
