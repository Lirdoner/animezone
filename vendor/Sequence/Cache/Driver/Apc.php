<?php


namespace Sequence\Cache\Driver;


use Sequence\Cache\DriverInterface;

class Apc implements DriverInterface
{
    protected $prefix;

    /**
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if(empty($options['prefix']))
        {
            throw new \InvalidArgumentException('Missing prefix argument.');
        }

        $this->prefix = $options['prefix'];
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if($this->exists($name))
        {
            return unserialize(apc_fetch($this->prefix.$name));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = null)
    {
        return apc_store($this->prefix.$name, serialize($value), $ttl ?: 0);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return apc_exists($this->prefix.$name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return apc_delete($this->prefix.$name);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($group)
    {
        $iterator = new \APCIterator('user', '/^'.$this->prefix.$group.'\.*/');

        foreach($iterator as $row)
        {
            apc_delete($row['key']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll()
    {
        $iterator = new \APCIterator('user', '/^'.$this->prefix.'\.*/');

        foreach($iterator as $row)
        {
            apc_delete($row['key']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'apc';
    }
} 