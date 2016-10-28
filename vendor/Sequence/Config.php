<?php


namespace Sequence;


/**
 * Class Config
 * @package Sequence
 */
class Config
{
    protected $data = array();

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $this->data[$key] = new static($value);
            } else
            {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if($this->has($name))
        {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach($this->data as $key => $value)
        {
            if($value instanceof self)
            {
                $array[$key] = $value->toArray();
            } else
            {
                $array[$key] = $value;
            }
        }

        return $array;
    }
} 