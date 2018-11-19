<?php

namespace Navel\Model;

interface EntityInterface
{
    public function __get($name);
    public function __set($name, $value);
}
