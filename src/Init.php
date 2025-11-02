<?php

namespace Src;

class Init
{
    public static function getServices(){
        return [
            Admin\AdminPages::class,
            Admin\AdminLinks::class,
            Admin\AdminInit::class,
            Base\FormShortcode::class,
            Base\Enqueue::class,
            Paynow\FormHandler::class,
            Paynow\NotificationHandler::class
        ];
    }
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