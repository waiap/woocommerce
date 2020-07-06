(function($){
  $(document).ready( function() {
    const client = new PWall(window.ezenit.environment, true);
    const checkout = client.checkout();
    initialize();

    function initialize(){
      updateCheckoutField();
      renderPaymentWall();
      addListeners();
      $(document).ready(function () {
        if (client.parseUrlParams('request_id') && client.parseUrlParams('method')) {
          $("#terms").click();
          window.location.hash = window.ezenit.waiap_hash;
        }
      }.bind(this));
    }

    function renderPaymentWall() {
      log("RENDERING PAYMENT WALL");
      checkout.appendTo("#waiap-app")
        .backendUrl(window.ezenit.backend_rest)
        .validateForm(checkCheckoutFields.bind(this))
        .submitButton('#place_order')
        .validateFields({
          "#customer_details" : checkCheckoutFields.bind(this),
          "#order_review": checkCheckoutFields.bind(this)
        })
        .on("beforeValidation", updateCustomerData.bind(this))
        .on("paymentOk", redirectToCheckoutSuccess.bind(this));
      updatePaymentWallDataset(checkout, true);
    }

    function addListeners(){
      $('body').on('invalid_fields', function() {
        renderWarning();
      }.bind(this));
      $('form.checkout').on('click', 'input[name="payment_method"]', function(){
          checkout.isSelected($('#' + window.ezenit.waiap_id).is(':checked'));
      });
      $('body').on('updated_checkout', function() {
        updatePaymentWallDataset(checkout);
      }.bind(this));
    }

    function updateCustomerData(){
      var serializeForm = $('form.checkout :not(#_wpnonce)').serialize();
      $.ajax({
        url: window.ezenit.checkout_rest,
        type:'POST',
        data: serializeForm,
        async: false
      }).done(function(data){
        console.log("OK");
      }).fail(function(data){
        console.log("FAIL");
      });
    }

    function updateCheckoutField() {
      $.ajax({
        url: window.ezenit.checkout_data,
        type: 'GET',
        async: false,
      }).done(function (data) {
        console.log("OK");
        try{
          var array_data = JSON.parse(data);
        }catch(e){
          
        }
        $.each(array_data, function(key, value){
          var input = $('[name='+key+']');
          if(input.val() === ''){
            input.val(value);
            input.trigger("change");
          }
        });
      }).fail(function (data) {
        console.log("FAIL");
      });
    }

    function updatePaymentWallDataset(checkout, isSet = false) {
      $.ajax({
        url: window.ezenit.quote_rest,
        async: false
      }).done(function (data) {
        if(isSet){
          checkout.groupId(data.groupId)
            .currency(data.currency)
            .isSelected($('#' + window.ezenit.waiap_id).is(':checked'))
        }else{
          if(checkout.saleAmount !== parseInt(data.amount*100)){
            checkout.amount(data.amount)
          }
        }        
      }).fail(function (data) {
        log("FAILED TO RETRIEVE QUOTE INFO")
      });
    } 

    function renderWarning(){
      $("#waiap-app").empty();
      $("#waiap-app").append(window.ezenit.form_check_lang);
    }


    function checkCheckoutFields(){
      var valid = true;
      $(".validate-required").each(function () {
        var validate_elements = $(this).find('.input-text, select, input:checkbox');
        if(validate_elements.length != 0){
          $(this).find('.input-text, select, input:checkbox').trigger("validate")
          valid = valid && ($(this).hasClass("woocommerce-validated") || $(this).is(":hidden"));
        }
      });
      console.log("FIELDS VALIDATION    ---->    "  + valid);
      if(!valid){
        $('body').trigger('invalid_fields');
      }
      return valid;
    }

    function redirectToCheckoutSuccess(){
      var url_encoded = getCookie("success_redirect");
      deleteCookie("success_redirect");
      window.location.replace(decodeURIComponent(url_encoded));
    }

    /**
     * Get the value of a cookie
     * Source: https://gist.github.com/wpsmith/6cf23551dd140fb72ae7
     * @param  {String} name  The name of the cookie
     * @return {String}       The cookie value
     */
    function getCookie(name) {
    	var value = "; " + document.cookie;
    	var parts = value.split("; " + name + "=");
    	if (parts.length == 2) return parts.pop().split(";").shift();
    };

    function deleteCookie(name) {
      document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    };

    function log(){
        var args = Array.prototype.slice.call(arguments, 0);
        args.unshift("[SIPAY DEBUG]");
        console.log.apply(console, args);
    }
  })
})(jQuery);
