<?php

namespace PWall\Helper;

class Constants{
  //API URLS
  const SANDBOX_URL = "https://sandbox.sipay.es/pwall/api/v1/actions";
  const LIVE_URL    = "https://live.waiap.com/pwall/api/v1/actions";

  const ENVIROMENTS_URLS = [
    "sandbox" => self::SANDBOX_URL,
    "live"    => self::LIVE_URL
  ];

  //API ACTIONS
  const PWALL_ACTION_SALE = "pwall.sale";
}