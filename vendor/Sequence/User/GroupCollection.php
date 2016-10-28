<?php

namespace Sequence\User;


/**
 * Class GroupCollection
 * @package Sequence\User
 */
class GroupCollection
{
    protected $collection;
    protected $_roles;
    protected $_groupNames;

    /**
     * Constructor
     *
     * @param array $groups
     */
    public function __construct(array $groups = array())
    {
        $this->collection = array();
        $this->addCollection($groups);
    }

    /**
     * @param Group $group
     */
    public function add(Group $group)
    {
        $this->collection[$group->getName()] = $group;
    }

    /**
     * @param $name
     * @return Group|false
     */
    public function get($name)
    {
        if(isset($this->collection[$name]))
        {
            return $this->collection[$name];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        if(null === $this->_roles)
        {
            $roles = array();

            foreach($this->collection as $group)
            {
                foreach($group->getRoles() as $groupRoles)
                {
                    $roles[$groupRoles] = 1;
                }
            }

            $this->_roles = $roles;
        }

        return $this->_roles;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if(null === $this->_groupNames)
        {
            $this->_groupNames = array();

            foreach($this->collection as $group)
            {
                $this->_groupNames[$group->getName()] = $group->getRealName();
            }
        }

        return $this->_groupNames;
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        if(isset($this->collection[$name]))
        {
            unset($this->collection[$name]);
        }
    }

    /**
     * @param array $groups
     */
    public function addCollection(array $groups)
    {
        foreach($groups as $group)
        {
            $this->add(new Group($group));
        }
    }
} 