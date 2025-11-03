<?php

namespace Src;

class Init
{
    /**
     * returns all of the predefined services that should be instantiated
     * @return string[]
     */
    public static function getServices(){
        return [
            Admin\AdminPages::class, // load the admin pages
            Admin\AdminLinks::class, // create the settings and history links on plugin page
            Admin\AdminInit::class, // register custom settings
            Base\FormShortcode::class, // create the donation form shorcode [paynow_donations_form]
            Base\PaymentReturnShorcode::class, // create the custom return screen shortcode [paynow_return]
            Base\Enqueue::class, // enqueue css files
            Paynow\FormHandler::class, // handle form submission
            Paynow\NotificationHandler::class, // handle incoming paynow notifications
        ];
    }

    /**
     * calls the register function on all of the classes defined in the getServices() function
     * @return void
     */
    public static function register_services(){
        foreach (self::getServices() as $class){
            $service = self::instantiate($class);
            if ( method_exists($service, 'register')){
                $service->register();
            }
        }
    }

    private static function instantiate($class){
        return new $class;
    }
}