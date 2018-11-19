<?php

namespace Navel\Core;

/**
 * Description of Singleton
 *
 * @author Julien
 */
class Singleton
{
    protected static $_INSTANCE = null;

    /**
     * Return the Singleton inherited instance object
     * @return Mixed
     */
    public static function getInstance()
    {
        if (!(static::$_INSTANCE instanceof static)) {
            static::$_INSTANCE = new static();
        }
        return static::$_INSTANCE;
    }

    final private function __clone() { }

    protected function __construct() { }
}
