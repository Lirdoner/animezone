<?php


namespace Sequence\Cache\Driver;


use Sequence\Cache\DriverInterface;

class Dummy implements DriverInterface
{
    public function get($name)
    {
        return null;
    }

    public function set($name, $value, $ttl = null)
    {
        return true;
    }

    public function exists($name)
    {
        return true;
    }

    public function delete($name)
    {
        return true;
    }

    public function deleteGroup($group)
    {
        return true;
    }

    public function deleteAll()
    {
        return true;
    }

    public function getName()
    {
        return 'dummy';
    }
} 