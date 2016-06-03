<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Result extends Db
{
    const BETTABLENAME = 'bets';    
    
    static protected $instance;
    
    public static function i()
    {
        return isset(static::$instance) ? static::$instance : (static::$instance = new static());
    }
}