<?php

namespace Sequence\Cache;


interface DriverInterface
{
    /**
     * Retrieves the value associated to the $name from the cache
     *
     * Must return NULL if the $name does not exists.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Stores a $value in the cache under the specified $name.
     * Overwrite any existing $name.
     *
     * @param string $name
     * @param mixed $value
     * @param int|null $ttl
     *
     * @return bool
     */
    public function set($name, $value, $ttl = null);

    /**
     * Checks if the $name exists
     *
     * @param string $name
     *
     * @return mixed
     */
    public function exists($name);

    /**
     * Deletes an $name from the cache
     *
     * @param string $name
     *
     * @return mixed
     */
    public function delete($name);

    /**
     * Deletes $group from the cache
     *
     * @param string $group
     *
     * @return mixed
     */
    public function deleteGroup($group);

    /**
     * Deletes all data from the cache
     *
     * @return mixed
     */
    public function deleteAll();

    /**
     * Return a name of the driver
     *
     * @return string
     */
    public function getName();
}