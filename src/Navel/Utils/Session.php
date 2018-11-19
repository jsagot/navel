<?php

namespace Navel\Utils;

use \Navel\Core\Singleton;

/**
 * Description of Session
 *
 * @author Julien
 */
class Session extends Singleton implements \ArrayAccess
{
    protected static $_INSTANCE = null;

    public function keyExists($key)
    {
        return $this->offsetExists($key);
    }

    public function get($key)
    {
        return $this->offsetGet($key);
    }

    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function unsetKey($key)
    {
        $this->offsetUnset($key);
    }

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return $_SESSION[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $_SESSION[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if($this->offsetExists($offset))
        {
            unset($_SESSION[$offset]);
        }
    }

    protected function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }
    }
}
