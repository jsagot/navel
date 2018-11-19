<?php

namespace Navel\Core;

/**
 * Description of IocContainer
 *
 * @author Julien SAGOT
 */
abstract class IocContainer
{
    private $registry = array();
    private $factory = array();
    private $instances = array();

    public function isLoaded($key)
    {
        return isset($this->instances[$key]);
    }

    public function set($key, Callable $resolver)
    {
        $this->registry[$key] = $resolver;
    }

    public function setFactory($key, Callable $resolver)
    {
        $this->factory[$key] = $resolver;
    }

    public function setInstance($instance)
    {
        $reflection = new ReflectionClass($instance);
       $this->instances[$reflection->getName()] = $instance;
    }

    public function get($key)
    {
        if(isset($this->factory[$key]))
        {
            return $this->factory[$key]();
        }

        if(!isset($this->instances[$key]))
        {
            if(isset($this->registry[$key]))
            {
                $this->instances[$key] = $this->registry[$key]();
            } else {
                $reflected_class = new ReflectionClass($key);
                if($reflected_class->isInstantiable())
                {
                    $constructor = $reflected_class->getConstructor();
                    if($constructor)
                    {
                        $parameters = $constructor->getParameters();
                        $construct_params = array();
                        foreach ($parameters as $param) {
                            if($param->getClass())
                            {
                                $construct_params[] = $this->get($param->getClass()->getName());
                            } else {
                                $construct_params[] = $param->getDefaultValue();
                            }
                        }
                        $this->instances[$key] = $reflected_class->newInstanceArgs($construct_params);
                    } else {
                        $this->instances[$key] = $reflected_class->newInstance();
                    }
                } else {
                    throw new Exception("[IocContainer] '$key' is not an Instanciable Class");
                }
            }
        }

        return $this->instances[$key];
    }
}
