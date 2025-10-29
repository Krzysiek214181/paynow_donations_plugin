<?php

namespace Src\Admin;

use \Src\Base\BaseController;
class AdminPages extends BaseController
{
    public function register(){
        add_action( 'admin_menu', [$this, 'add_admin_pages']);
    }

    public function add_admin_pages(){
        add_menu_page('Paynow History', 'Paynow', 'manage_options', 'paynow_donations', [$this, 'admin_history'], 'dashicons-money-alt', 100);
        add_submenu_page('paynow_donations', 'Paynow Settings', 'Settings', 'manage_options', 'paynow_donations_settings', [$this, 'admin_settings']);
    }

    public function admin_history(){
        require_once $this->plugin_path . "templates/admin-history.php";
    }

    public function admin_settings() {
        require_once $this->plugin_path . "templates/admin-settings.php";
    }
}