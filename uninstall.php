<?php

/**
*   @package PaynowDonations
*/

if( !defined('WP_UNINSTALL_PLUGIN')){
    die;
}

//delete paynow keys
delete_option('donations_for_paynow_apiKey');
delete_option('donations_for_paynow_signatureKey');
delete_option('donations_for_paynow_environment');
delete_option('donations_for_paynow_debug');

//delete options group
delete_option('donations_for_paynow_settings_group');

//drop table
global $wpdb;

$table_name = $wpdb->prefix . 'donations_for_paynow';
$debug_table_name = $wpdb->prefix .'donations_for_paynow_debug';
$wpdb->query("DROP TABLE IF EXISTS $table_name");
$wpdb->query("DROP TABLE IF EXISTS $debug_table_name");