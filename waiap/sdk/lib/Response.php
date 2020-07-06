<?php

declare(strict_types=1);

namespace PWall;

class Response
{
  protected $response;

  public function __construct(
    $jsonResponse
  ){
    $this->response = json_decode($jsonResponse, true);
  }

  /**
   * Returns the JSON response
   *
   * @return string JSON response
   */
  public function toJSON(){
    return json_encode($this->response);
  }

  /**
   * Returns error code if there was an error
   *
   * @return string error code if there was an error, null otherwise
   */
  public function getErrorCode(){
    if(array_key_exists("result", $this->response)
    && array_key_exists("code", $this->response["result"])
    && $this->response["result"]["code"] !== 0){
      return $this->response["result"]["code"];
    }

    if(array_key_exists("code", $this->response)
    && $this->response["code"] !== 0){
      return $this->response["code"];
    }

    return null;
  }
  
  /**
   * Returns error message if there was an error
   *
   * @return String error message if there was an error, null otherwise
   */
  public function getErrorMessage(){
    if(array_key_exists("result", $this->response)
    && array_key_exists("code", $this->response)
    && $this->response["result"]["code"] !== 0){
      return $this->response["result"]["description"];
    }

    if(array_key_exists("code", $this->response)
    && $this->response["code"] !== 0){
      return $this->response["description"];
    }

    return null;
  }
  
  /**
   * Check if the response is valid
   *
   * @return boolean true if response is valid, otherwise false
   */
  public function isValid(){
    if(is_array($this->response)
    && array_key_exists("id", $this->response)
    && array_key_exists("result", $this->response)){
      return true;
    }
    return false;
  } 

  /**
   * This response checks if the response is valid for place order
   *
   * @return boolean true if can place order, otherwise false
   */
  public function canPlaceOrder(){
    return false;
  }
  
  /**
   * Returns the paid amount if the response is for sale action
   *
   * @return float|null amount paid by customer, null if response is not for sale action
   */
  public function getPaidAmount(){
    return null;
  }

  /**
   * Returns the payment method used if the response is for sale action
   *
   * @return string|null payment method name used by customer, null if response is not for sale action
   */
  public function getPaymentMethod(){
    return null;
  }

  /**
   * Returns the payment info in a flattened array
   *
   * @return array|null payment info in a flatenned array, null if response is not for sale action
   */
  public function getPaymentInfo(){
    return null;
  }
}
