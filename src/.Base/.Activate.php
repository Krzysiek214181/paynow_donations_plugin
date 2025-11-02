<?php

namespace Src\Base;

use Src\Base\DbService;

class Activate
{
    public static function activate(){
        $dbService = new DbService();
        $dbService->register();
    }
}