<?php
/**
 * @package Waiap_PaymentWall
 * @version 4.0.6
 */
 /*
 Plugin Name: Waiap: Payment Wall (el muro de pagos)
 Description: Con el Payment Wall de Waiap puedes aceptar múltiples formas de pago (Tarjetas Visa, MasterCard…, Amazon Pay, PayPal, Google Pay, Apple Pay, Bizum, pago con Cuenta y pago con Financiación) en un único módulo.
 Author: Bankia
 Author URI:        https://www.bankia.es/es/empresas/cobros-y-pagos/tpv-ecommerce/waiap/documentacion
 Version: 4.0.6
 Text Domain:       woocommerce-payment-wall
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

require_once __DIR__ . '/sdk/autoload.php';

class WC_Waiap_Payment{

  public function init(){
    if (class_exists('WC_Payment_Gateway')) {
      define('__ROOT__', dirname(__FILE__));
      require_once __ROOT__.'/model/waiap-admin-paymentwall.php';
      require_once __ROOT__.'/controllers/waiap-backend.php';
      require_once __ROOT__.'/controllers/waiap-checkout.php';
      require_once __ROOT__.'/controllers/waiap-quote.php';
      include_once __ROOT__.'/logger/waiap-payment-log.php';
      if (!session_id()) {
          session_start();
          if (! isset(WC()->session)) {
              WC()->session = new WC_Session_Handler();
              if(method_exists(WC()->session, "init")){//Compatibility for 3.0-3.6
                WC()->session->init();
              }
          }
      }
      add_filter('woocommerce_payment_gateways', function ($methods) {
        $methods[] = 'WC_Waiap_Paymentwall';
        return $methods;
        }
      );
      add_action(
          'rest_api_init',
          function () {
              register_rest_route(
                  'waiap/v1',
                  '/actions', array(
                      'methods' => 'POST',
                      'callback' => function ($request) {
                          $pwall = new WC_Waiap_Backend();
                          return $pwall->actions($request);
                      },
                      'args' => array(
                          'method',
                          'request_id'
                      ),
                      'permission_callback' => '__return_true',
                  )
              );
          }
      );
      add_action(
          'rest_api_init',
          function () {
              register_rest_route(
                  'waiap/v1',
                  '/quote', array(
                      'methods' => 'POST, GET',
                      'callback' => function ($request) {
                          $pwall = new WC_Waiap_Quote();
                          return $pwall->getQuoteInfo($request);
                      },
                      'args' => array(),
                      'permission_callback' => '__return_true',
                  )
              );
          }
      );
      add_action(
          'rest_api_init',
          function () {
              register_rest_route(
                  'waiap/v1',
                  '/checkout', array(
                      'methods' => 'POST, GET',
                      'callback' => function ($request) {
                          $pwall = new WC_Waiap_Checkout();
                          return $pwall->setCheckoutInfo($request);
                      },
                      'args' => array(
                        'checkout_data'
                      ),
                      'permission_callback' => '__return_true',
                  )
              );
          }
      );
      add_action(
          'rest_api_init',
          function () {
              register_rest_route(
                  'waiap/v1',
                  '/checkout_data', array(
                      'methods' => 'POST, GET',
                      'callback' => function ($request) {
                          $pwall = new WC_Waiap_Checkout();
                          return $pwall->getCheckoutInfo($request);
                      },
                      'permission_callback' => '__return_true',
                  )
              );
          }
      );
    }
  }
}

add_action('init', array(new WC_Waiap_Payment(), 'init'));
