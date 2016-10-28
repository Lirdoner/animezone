<?php

namespace Sequence\Cache\Driver;


class File extends PlainFile
{
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $value = parent::get($name);

        return null !== $value ? unserialize($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = 3600)
    {
        return parent::set($name, serialize($value), $ttl);
    }
}