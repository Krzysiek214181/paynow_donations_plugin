<?php

namespace Src\Admin;

use \Src\Base\BaseController;
class AdminPages extends BaseController
{
    public static $adminSubPages = [];
    public function register(){
        // add history subpage ( same as main page ) for better menu title
        $this->add_subpage(
        [
            'page_title' => 'Paynow History', 
            'menu_title' => 'History', 
            'callback' => [$this, 'admin_history'], 
            'capabilities' => 'edit_others_posts'
        ]);
        // add settings subpage
        $this->add_subpage( 
        [
            'page_title' => 'Paynow Settings', 
            'menu_title' => 'Settings', 
            'menu_slug' => 'paynow_settings', 
            'callback' => [$this, 'admin_settings'], 
            'capabilities' => 'edit_others_posts'
        ]);

        // if debug is on, add debug subpage
        if(get_option('paynow_debug')){
            $this->add_subpage(
            [
                'page_title' => 'Paynow Debug', 
                'menu_title' => 'Debug', 
                'menu_slug' => 'paynow_debug', 
                'callback' => [$this, 'admin_debug']]);
        }

        add_action( 'admin_menu', [$this, 'add_admin_pages']);
    }

    public function add_admin_pages(){
        //add main admin page ( history page )
        add_menu_page('Paynow History', 'Paynow', 'edit_others_posts', 'paynow_donations', [$this, 'admin_history'], 'dashicons-money-alt', 100);

        //add all subpages defined using add_subpage() function
        foreach(self::$adminSubPages as $page){
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capabilites'], $page['menu_slug'], $page['callback']);
        }
    }

    /**
     * adds a subpage to the adminSubPages array
     * @param array{
     *      parent_slug: string,
     *      page_title: string,
     *      menu_title: string,
     *      capabilites: string,
     *      menu_slug: string,
     *      callback: callable
     * } $args
     * @return void
     */
    public function add_subpage(array $args){
        $defaults = [
            'parent_slug' => 'paynow_donations',
            'page_title' => 'Paynow Title',
            'menu_title' => 'Title',
            'capabilites' => 'manage_options',
            'menu_slug' => 'paynow_donations',
            'callback' => function() {echo "set callback in add_subpage";}
        ];

        $mergedArgs = array_merge($defaults, $args);

        self::$adminSubPages[] = [
            'parent_slug' => $mergedArgs['parent_slug'],
            'page_title' => $mergedArgs['page_title'],
            'menu_title' => $mergedArgs['menu_title'],
            'capabilites' => $mergedArgs['capabilites'],
            'menu_slug' => $mergedArgs['menu_slug'],
            'callback' => $mergedArgs['callback']
        ];
    }

    /**
     * admin_history subpage callback
     * @return void
     */
    public function admin_history(){
        require_once $this->plugin_path . "templates/admin-history.php";
    }

    /**
     * admin_settings subpage callback
     * @return void
     */
    public function admin_settings() {
        require_once $this->plugin_path . "templates/admin-settings.php";
    }

    public function admin_debug(){
        require_once $this->plugin_path . "templates/admin-debug.php";
    }
}