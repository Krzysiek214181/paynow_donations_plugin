<?php

namespace Kszkl\Donations\Base;

use Kszkl\Donations\Base\BaseController;
class Enqueue extends BaseController
{
    public function register(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
    }

    public function enqueue_public_styles(){
        wp_enqueue_style(
            'donations_for_paynow_styles',
            $this->plugin_url .  'assets/public-styles.css',
            [],
            '1.1'
        );
    }

    public function enqueue_admin_styles(){
        wp_enqueue_style(
            'donations_for_paynow_styles',
            $this->plugin_url .  'assets/admin-styles.css'
        );
    }
}