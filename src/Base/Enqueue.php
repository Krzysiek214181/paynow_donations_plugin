<?php

namespace Src\Base;

use Src\Base\BaseController;
class Enqueue extends BaseController
{
    public function register(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
    }

    public function enqueue_public_styles(){
        wp_enqueue_style(
            'paynow_donations_styles',
            $this->plugin_url .  'assets/public-styles.css'
        );
    }

    public function enqueue_admin_styles(){
        wp_enqueue_style(
            'paynow_donations_styles',
            $this->plugin_url .  'assets/admin-styles.css'
        );
    }
}