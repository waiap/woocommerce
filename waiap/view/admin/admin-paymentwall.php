<?php

if (!function_exists("enqueue_admin_scripts")) {
  function enqueue_admin_scripts($environment)
  {
    wp_enqueue_script('waiap-sdk', $environment . '/pwall_sdk/pwall_sdk.bundle.js', array(), '1.0', true);
    wp_enqueue_script('waiap-app-sdk', 'https://assets-sipay.s3-eu-west-1.amazonaws.com/sdk-js/pwall-sdk.min.js', array(), '1.0', false);
  }
}
if (!function_exists("enqueue_styles")) {
  function enqueue_styles($environment)
  {
    wp_enqueue_style('pwall-css', $environment . '/pwall_app/css/app.css');
  }
}
if (!function_exists("waiap_admin_paymentwall")) {
  function waiap_admin_paymentwall($enviroment)
  {
?>

    <script>
      // Create the div
      var div = document.createElement('div');
      div.setAttribute('id', 'waiap-app');

      // Append div to form
      document.querySelector('#woocommerce_waiap_woocommerce_0').parentElement.appendChild(div);

      document.querySelector('#waiap-app').style.background = "#FFF";

      // Remove unused field
      var elem = document.querySelector('#woocommerce_waiap_woocommerce_0');
      elem.parentNode.removeChild(elem);

      const client = new PWall('<?php echo $enviroment ?>', false);
      var backoffice = client.backoffice();
      backoffice.backendUrl("<?php echo get_rest_url(null, '/waiap/v1/actions') ?>");
      backoffice.appendTo("#waiap-app");
      backoffice.init();
    </script>
<?php }
}
?>