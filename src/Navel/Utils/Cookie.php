<?php

namespace Navel\Utils;

use \Navel\Core\Singleton;

/**
 * Description of Cookie
 *
 * @author Julien
 */
class Cookie extends Singleton implements \ArrayAccess
{
    protected static $_INSTANCE = null;

    public function keyExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    public function get($key)
    {
        return $_COOKIE[$key];
    }

    public function set($key, $value)
    {
        $expire = eval("return (".COOKIES_LIFETIME.");");
        setcookie($key, $value, $expire, "/");
    }

    public function unsetKey($key)
    {
        if($this->offsetExists($key))
        {
            setcookie($key, '', time()-1000);
        }
    }

    public function offsetExists($offset)
    {
        return isset($_COOKIE[$offset]);
    }

    public function offsetGet($offset)
    {
        return $_COOKIE[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $expire = eval("return (".COOKIES_LIFETIME.");");
        setcookie($offset, $value, $expire, "/");
    }

    public function offsetUnset($offset)
    {
        if($this->offsetExists($offset))
        {
            setcookie($offset, '', time()-1000);
        }
    }

}
