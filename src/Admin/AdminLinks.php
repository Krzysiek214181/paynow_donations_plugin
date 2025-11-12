<?php

namespace Kszkl\Donations\Admin;

use Kszkl\Donations\Base\BaseController;

class AdminLinks extends BaseController
{
    public function register(){
        add_filter( "plugin_action_links_" . $this->plugin_name, [$this, 'add_links']);
    }

    public function add_links($links){
        $settings_link = '<a href="admin.php?page=donations_for_paynow">History</a>';
        $history_link = '<a href="admin.php?page=donations_for_paynow_settings">Settings</a>';
        array_push( $links, $history_link, $settings_link);
        return $links;
    }
}