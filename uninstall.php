<?php

/**
*   @package PaynowDonations
*/

if( !defined('WP_UNINSTALL_PLUGIN')){
    die;
}

//delete paynow keys
delete_option('paynow_apiKey');
delete_option('paynow_signatureKey');

//delete options group
delete_option('paynow_donations_settings_group');