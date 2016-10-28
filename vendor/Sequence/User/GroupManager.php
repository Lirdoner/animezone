<?php

namespace Sequence\User;

use Sequence\Database\Database;


/**
 * Class GroupManager
 * @package Sequence\User
 */
class GroupManager
{
    /**
     * @var \Sequence\Database\Database
     */
    protected $database;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param Group $group
     * @return \PDOStatement
     */
    public function deleteGroup(Group $group)
    {
        return $this->database->delete('groups')->leftJoin('user_groups', 'name=group_name')->where('name', $group->getName())->get();
    }

    /**
     * @param Group $group
     * @return \PDOStatement
     */
    public function updateGroup(Group $group)
    {
        if(false === $this->findGroupByName($group->getName()))
        {
            return $this->database->insert('groups', array(
                    'name' => $group->getName(),
                    'roles' => $group->getRoles(true),
                    'real_name' => $group->getRealName(),
                    'description' => $group->getDescription()
                ))->get();
        } else
        {
            return $this->database->update('groups', array(
                    'name' => $group->getName(),
                    'roles' => $group->getRoles(true),
                    'real_name' => $group->getRealName(),
                    'description' => $group->getDescription()
                ))->where(array('name' => $group->getName()))->get();
        }
    }

    /**
     * @param string $name
     * @return Group
     */
    public function createGroup($name)
    {
        return new Group(array('name' => $name));
    }

    /**
     * @param string $name
     * @return bool|Group
     */
    public function findGroupByName($name)
    {
        return $this->findGroupBy(array('name' => $name));
    }

    /**
     * @param array $criteria
     * @return bool|Group
     */
    public function findGroupBy(array $criteria)
    {
        $group = $this->findGroups($criteria)->limit(1)->get();
        
        if($group->rowCount())
        {
			return new Group($group->fetch());
        }

        return false;
    }

    /**
     * @param array $criteria an array with criteria field => value
     * @return Database
     */
    public function findGroups(array $criteria = array())
    {
        return $this->database->select()->from('groups')->where($criteria);
    }
}