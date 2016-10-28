<?php


namespace Sequence\Database\Entity;


/**
 * Class BaseModel
 * @package Sequence\Database\Entity
 */
abstract class BaseEntity implements EntityInterface
{
    /** @var array  */
    protected $guarded = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_setData($data);
    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function _setData(array $data)
    {
        foreach($data as $key => $value)
        {
            if(!property_exists($this, $key))
            {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist.', $key));
            } else
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $vars = get_object_vars($this);
        $this->guarded[] = 'guarded';

        foreach($this->guarded as $key)
        {
            unset($vars[$key]);
        }

        return $vars;
    }
} 