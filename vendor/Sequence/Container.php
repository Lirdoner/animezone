<?php

namespace Sequence;


/**
 * Class Container
 */
class Container
{
    /**
     * @var array
     */
    protected $services;

    /**
     * @param array $services
     */
    public function __container(array $services = null)
    {
        $this->services = $services;
    }

    /**
     * @param $id
     * @param $service
     */
    public function set($id, $service)
    {
        $this->services[$id] = $service;
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($id)
    {
        if(false === $this->has($id))
        {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        if(method_exists($this->services[$id], '__invoke'))
        {
            return $this->services[$id] = $this->services[$id]($this);
        }

        return $this->services[$id];
    }
} 