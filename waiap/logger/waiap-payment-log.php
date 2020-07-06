<?php

class WC_Waiap_Payment_Log
{
    /**
     * Log
     *
     * @param string $s string to send to log
     *
     * @return null
     **/
    public static function log($s)
    {
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
          $logger = wc_get_logger();
          $logger->debug($s, array( 'source' => 'waiap' ) );
        }
    }
}
