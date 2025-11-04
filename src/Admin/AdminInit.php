<?php

namespace Src\Admin;

class AdminInit
{
    public function register(){
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('allowed_redirect_hosts', [$this, 'add_allowed_hosts']);
    }

    public function register_settings(){
        register_setting('donations_for_paynow_settings_group', 'donations_for_paynow_apiKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('donations_for_paynow_settings_group', 'donations_for_paynow_signatureKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('donations_for_paynow_settings_group', 'donations_for_paynow_environment', [
            'sanitize_callback' => 'rest_sanitize_boolean',
            'type' => 'boolean',
            'default' => false
        ]); // 0 - SANDBOX / 1 - PRODUCTION
        register_setting('donations_for_paynow_settings_group', 'donations_for_paynow_debug', [
            'sanitize_callback' => 'rest_sanitize_boolean',
            'type' => 'boolean',
            'default' => false
        ]); // 0 - OFF / 1 - ON
    }

    public function add_allowed_hosts($hosts){
        $hosts[] = 'paywall.sandbox.paynow.pl';
        $hosts[] = 'paywall.paynow.pl';
        return $hosts;
    }
}