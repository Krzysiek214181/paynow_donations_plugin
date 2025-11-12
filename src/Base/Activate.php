<?php

namespace Kszkl\Donations\Base;

use Kszkl\Donations\Base\DbService;

class Activate
{
    public static function activate(){
        $dbService = new DbService();
        $dbService->register();
    }
}