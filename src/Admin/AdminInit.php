<?php

namespace Src\Admin;

class AdminInit
{
    public function register(){
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings(){
        register_setting('paynow_donations_settings_group', 'paynow_apiKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('paynow_donations_settings_group', 'paynow_signatureKey', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        register_setting('paynow_donations_settings_group', 'paynow_enivronment', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
    }
}