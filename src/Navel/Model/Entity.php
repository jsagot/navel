<?php

namespace Navel\Model;

class Entity implements EntityInterface
{
    protected $id = -1;

    public function getMembers()
    {
        $tmp = get_object_vars($this);
        $last = array_pop($tmp);
        return  ['id' => $last] + $tmp;
    }

    public function __get($name)
    {
        if(isset($this->$name))
        {
            return $this->$name;
        } else {
            throw new \RuntimeException(get_called_class()." does not have member $name");
        }
    }

    public function __set($name, $value)
    {
        if (gettype($this->$name) === gettype($value))
        {
            $this->$name = $value;
        } else {
            throw new \InvalidArgumentException(get_called_class().'->'.$name.' expects to be '.gettype($this->$name).', '.gettype($value).' given.');
        }
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }
}
