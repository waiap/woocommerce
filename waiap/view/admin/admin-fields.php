<?php
if( !function_exists("waiap_admin_payment_config") )
{
  function waiap_admin_payment_config(){
    $settings = array(
        'enabled' => array(
            'title'       => __('Enable/Disable', 'woocommerce-payment-wall'),
            'label'       => __(
                'Enable Waiap', 'woocommerce-payment-wall'
            ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'yes',
        ),
        'method_title' => array(
          'title'         => __('Payment method checkout title', 'woocommerce-payment-wall'),
          'type'          => 'text',
          'description'   => __(
              'Waiap payment method title used in checkout',
              'woocommerce-payment-wall'
          ),
          'default'       => 'Pagar con tarjeta y otros métodos de pago'
        ),
        'waiap_key' => array(
            'title'         => __('Commerce Key', 'woocommerce-payment-wall'),
            'type'          => 'password',
            'description'   => __(
                'Waiap provided commerce key', 'woocommerce-payment-wall'
            ),
            'default'       => 'commerce-key'
        ),
        'waiap_environment' => array(
            'title'         => __('Environment', 'woocommerce-payment-wall'),
            'type'          => 'select',
            'options'       => array(
                'sandbox' => 'sandbox',
                'live' => 'live'
            ),
            'description'   => __(
                'Environment. Sandbox is for integration '.
                'tests, live for real transactions.',
                'woocommerce-payment-wall'
            )
        ),
        'waiap_secret' => array(
            'title'         => __('Waiap secret', 'woocommerce-payment-wall'),
            'type'          => 'password',
            'description'   => __(
                'Waiap provided secret', 'woocommerce-payment-wall'
            ),
            'default'       => 'secret'
        ),
        'waiap_resource' => array(
            'title'         => __('Waiap resource', 'woocommerce-payment-wall'),
            'type'          => 'text',
            'description'   => __(
                'Waiap provided resource for payment wall all',
                'woocommerce-payment-wall'
            ),
            'default'       => 'resource'
        ),
        array(
            'name'    => __('extra', 'woocommerce-payment-wall'),
            'desc'    => __('Extra info, leave it empty.', 'woocommerce'),
            'id'      => "app",
            'type'    => 'pwalsettings'
        )
    );

    return apply_filters('wc_waiap_woocommerce_settings', $settings);
  }
}
