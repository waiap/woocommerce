<?php

require_once __ROOT__.'/view/index.php';
require_once __ROOT__.'/helpers/index.php';

class WC_Waiap_Paymentwall extends WC_Payment_Gateway
{

  public function __construct(){
    $this->id                 = 'waiap_woocommerce';
    $this->method_title       = 'Waiap: Payment Wall (el muro de pagos)';
    $this->method_description = 'Con el Payment Wall de Waiap puedes aceptar múltiples formas de pago (Tarjetas Visa, MasterCard…, Amazon Pay, PayPal, Google Pay, Apple Pay, Bizum, pago con Cuenta y pago con Financiación) en un único módulo.';
    $this->supports           = array('products');
    $this->title              = $this->get_option('method_title');
    $this->has_fields         = true;
    $this->form_fields        = waiap_admin_payment_config();

    $this->init_settings();

    $this->enabled                      = $this->get_option('enabled');
    $this->environment                  = $this->get_option('waiap_environment');
    $this->key                          = $this->get_option('waiap_key');
    $this->resource                     = $this->get_option('waiap_resource');
    $this->secret                       = $this->get_option('waiap_secret');

    $environment = $this->getEnvironmentUrl();

    //checkout resources
    add_action('wp_footer', function() use ($environment) {enqueue_styles($environment);});
    add_action('wp_footer', function() use($environment){checkout_enqueue_scripts($environment, $this->environment);});
    //admin resources
    add_action('admin_enqueue_scripts', function() use ($environment) {enqueue_styles($environment);});
    add_action('admin_enqueue_scripts', function() use($environment){enqueue_admin_scripts($environment);});
    add_action('admin_footer', function() use ($environment) {waiap_admin_paymentwall($this->environment);});
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this,'process_admin_options'));
  }

  private function getEnvironmentUrl(){
    if ($this->environment == 'sandbox') {
      return 'https://sandbox.sipay.es';
    }
    return 'https://live.waiap.com';
  }

  public function payment_fields(){
    render_checkout_paymentwall($this->environment);
  }

  public function process_payment($order_id) {

  }
}
