<?php

namespace Src\Base;

use Src\Base\BaseController;
class Enqueue extends BaseController
{
    public function register(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles(){
        wp_enqueue_style(
            'paynow_donations_styles',
            $this->plugin_url .  'assets/styles.css'
        );
    }
}