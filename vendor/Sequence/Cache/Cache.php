<?php

namespace Sequence\Cache;



class Cache implements DriverInterface
{
    /** @var  \Sequence\Cache\DriverInterface */
    protected $driver;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @param bool $enabled
     */
    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param DriverInterface $driver
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if(!$this->enabled)
        {
            return null;
        }

        return $this->driver->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = null)
    {
        return $this->driver->set($name, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->driver->exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return $this->driver->delete($name);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($group)
    {
        return $this->driver->deleteGroup($group);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll()
    {
        return $this->driver->deleteAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->driver->getName();
    }
}