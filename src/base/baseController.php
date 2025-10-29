<?php

namespace Src\Base;

class BaseController 
{
    public $plugin_path;
    public $plugin_name;

    public function __construct(){
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_name = plugin_basename(dirname(__FILE__,3) . "/paynow_donations.php");
    }
}