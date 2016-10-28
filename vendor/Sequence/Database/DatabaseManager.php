<?php

namespace Sequence\Database;

/**
* @package		sequence
* @subpackage	database
* @author		Filip Szczechowiak <filip.szczechowiak@gmail.com>
*/
class DatabaseManager
{
    protected $connections = array();
    protected $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * @param string $id
     *
     * @return Database
     * @throws \InvalidArgumentException
     */
    public function getConnection($id)
    {
        if(isset($this->connections[$id]))
        {
            return $this->connections[$id];
        }

        return $this->connections[$id] = new Database($this->getConnectionSettings($id));
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getConnectionSettings($id)
    {
        if(isset($this->options[$id]))
        {
            return $this->options[$id];
        } else
        {
            throw new \InvalidArgumentException(sprintf('Missing connection "%s" in connections lists.', $id));
        }
    }

    /**
     * @param string $name
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function addConnection($name, array $options)
    {
        if(!isset($this->options[$name]))
        {
            $this->options[$name] = $options;
        } else
        {
            throw new \InvalidArgumentException(sprintf('Connection "%s" already exists.', $name));
        }
    }
}