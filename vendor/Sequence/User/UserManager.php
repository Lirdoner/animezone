<?php

namespace Sequence\User;

use Sequence\Database\Database;


/**
 * Class UserManager
 * @package Sequence\User
 */
class UserManager
{
    /** @var \Sequence\Database\Database  */
    protected $database;

    /** @var array  */
    protected $options;

    /**
     * @param Database $database
     * @param array $options available:
     *  * verification int 0 => disabled, 1 => enabled, 3 => blocked
     *  * groups bool true if groups are active
     */
    public function __construct(Database $database, $options = array())
    {
        $this->database = $database;
        $this->options = array_merge(array(
            'verification' => 1,
            'groups' => false,
        ), $options);
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function createUser(array $data = array())
    {
        $basic = array(
            'role' => User::ROLE_USER,
            'enabled' => (int)$this->options['verification']
        );

        return new User(array_replace_recursive($basic, $data));
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user)
    {
        $this->database->delete('users')->where(array('id' => $user->getId()))->get();
        $this->database->delete('users_online')->where(array('user_id' => $user->getId()))->get();

    	return true;
    }

    /**
     *
     *
     * @param User $user
     * @return bool
     */
    public function updateUser(User $user)
    {
        $find = $this->database->select()->from('users')->
            where(array('id' => $user->getId()))->
            orWhere(array('name' => $user->getUsername()))->
            orWhere(array('email ' => $user->getEmail()))->get();
        
        if(!$find->fetch())
        {
            $this->database->insert('users', $user->getUserData())->get();

            if(false !== $user->getCustomFields())
            {
                $this->database->insert('users_custom_field', array_merge(array('user_id' => $this->database->lastInsertId()), $user->getCustomFields()))->get();
            }
        } else
        {
            $this->database->update('users', $user->getUserData())->where(array('id' => $user->getId()))->get();

            if(false !== $user->getCustomFields())
            {
                if($this->database->select()->from('users_custom_field')->where(array('user_id' => $user->getId()))->get()->fetch())
                {
                    $this->database->update('users_custom_field', $user->getCustomFields())->where(array('user_id' => $user->getId()))->get();
                } else
                {
                    $this->database->insert('users_custom_field', array_merge(array('user_id' => $user->getId()), $user->getCustomFields()))->get();
                }
            }
        }
        
        return true;
    }

    /**
     * @param User $user
     *
     * @return \PDOStatement
     */
    public function updateLastLogin(User $user)
    {
        return $this->database->update('users', array(
            'last_login' => $user->getLastLogin()->format('Y-m-d H:i:s'),
        ))->where(array('id' => $user->getId()))->get();
    }

    /**
     * @param $email
     * @return bool|User
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('email' => $email));
    }

    /**
     * @param $username
     * @return bool|User
     */
    public function findUserByUsername($username)
	{
		return $this->findUserBy(array('name' => $username));
	}

    /**
     * @param $usernameOrEmail
     * @return bool|User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if(false !== filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
        {
            return $this->findUserByEmail($usernameOrEmail);
        }
        
        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * @param array $criteria only canonical fields @see User::$canonicalFields
     * @return bool|User
     */
    public function findUserBy(array $criteria)
    {
        $userData = $this->database->select()->from('users')->leftJoin('users_custom_field', 'id=user_id')->where($criteria)->get();
        
        if(false === $userData = $userData->fetch())
        {
            return false;
        }
        
        if($this->options['groups'])
        {
            $groups = $this->database->select()->from('groups')->innerJoin('users_groups', 'name=group_name')->where(array('user_id' => $userData['id']))->get();
            $group = array();
            
            foreach($groups->fetchAll() as $gr)
            {
                $group[$gr['name']] = $gr;
            }
            
            $userData['groups'] = $group;
        }

        return new User($userData);
    }

    /**
     * @param array $criteria
     * @return Database
     */
    public function findUsers(array $criteria = array())
    {
        $this->database->select()->from('users')->innerJoin('users_custom_field', 'id=user_id');
        
        if(!empty($criteria))
        {
            $this->database->where($criteria);
        }
        
        return $this->database;
    }
}