<?php
if(!function_exists('checkout_enqueue_scripts')){
  function checkout_enqueue_scripts($environment_url, $environment){
    wp_enqueue_script('waiap-sdk', $environment_url.'/pwall_sdk/pwall_sdk.bundle.js', array(), '1.3', false);
    wp_enqueue_script('waiap-app-sdk', 'https://assets-sipay.s3-eu-west-1.amazonaws.com/sdk-js/pwall-sdk.min.js', array(), '1.0', false);
    wp_enqueue_script('waiap-paymentwall', plugins_url('waiap/view/frontend/js/waiap-checkout-paymentwall.js'), array('jquery','waiap-sdk'), '1.3', true );
    wp_localize_script('waiap-paymentwall', 'ezenit', ["quote_rest" => get_rest_url(null, '/waiap/v1/quote'),
                                                       "checkout_rest" => get_rest_url(null, '/waiap/v1/checkout'),
                                                       "checkout_data" => get_rest_url(null, '/waiap/v1/checkout_data'),
                                                       "backend_rest" => get_rest_url(null, '/waiap/v1/actions'),
                                                       "nonce" => wp_create_nonce('wp_rest'),
                                                       "form_check_lang" => __('Check missing or invalid fields','waiap'),
                                                       "app_js" => $environment_url.'/pwall_app/js/app.js',
                                                       "environment" => $environment,
                                                       "waiap_id" => "payment_method_waiap_woocommerce",
                                                       "waiap_hash" => "waiap-app"]);
  }
}
if(!function_exists('render_checkout_paymentwall')){
  function render_checkout_paymentwall($environment){
    ?>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
    <div id=waiap-app></div>
    <?php
  }
}
?>
