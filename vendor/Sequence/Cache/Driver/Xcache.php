<?php


namespace Sequence\Cache\Driver;


use Sequence\Cache\DriverInterface;

class Xcache implements DriverInterface
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
    public function get($name, $serialize = false)
    {
        if($this->exists($name))
        {
            return unserialize(xcache_get($this->prefix.$name));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = null)
    {
        return xcache_set($this->prefix.$name, serialize($value), $ttl ?: 0);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return xcache_isset($this->prefix.$name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return xcache_unset_by_prefix($this->prefix.$name);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($group)
    {
        return xcache_unset_by_prefix($this->prefix.$group);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll()
    {
        return xcache_unset_by_prefix($this->prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xcache';
    }
} 