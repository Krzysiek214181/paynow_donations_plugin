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

//drop table
global $wpdb;

$table_name = $wpdb->prefix . 'paynow_donations_transactions';
$wpdb->query("DROP TABLE IF EXISTIS $table_name");