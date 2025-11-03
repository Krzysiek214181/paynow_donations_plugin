<?php

namespace Src\Admin;

class AdminInit
{
    public function register(){
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('allowed_redirect_hosts', [$this, 'add_allowed_hosts']);
    }

    public function register_settings(){
        register_setting('paynow_donations_settings_group', 'paynow_apiKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('paynow_donations_settings_group', 'paynow_signatureKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('paynow_donations_settings_group', 'paynow_environment', [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'boolean',
            'default' => '0'
        ]); // 0 - SANDBOX / 1 - PRODUCTION
        register_setting('paynow_donations_settings_group', 'paynow_debug', [
            'sanitize_callback' => 'sanitize_text_field',
            'type' => 'boolean',
            'default' => '0'
        ]); // 0 - OFF / 1 - ON
    }

    public function add_allowed_hosts($hosts){
        $hosts[] = 'paywall.sandbox.paynow.pl';
        $hosts[] = 'paywall.paynow.pl';
        return $hosts;
    }
}