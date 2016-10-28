<?php


namespace Sequence\User;


/**
 * Class UserDataParser
 * @package Sequence\User
 */
class UserDataParser
{
    /**
     * @var array
     */
    protected $basicData;

    /**
     * @var array
     */
    protected $customData;

    /**
     * @var \Sequence\User\GroupCollection
     */
    protected $groups;

    /**
     * @param array $userData
     */
    public function __construct(array $userData)
    {
        $this->basicData = array();
        $this->customData = array();

        $this->parse($userData);
    }

    /**
     * @param array $userData
     */
    protected function parse(array $userData)
    {
        if(empty($userData['groups']))
        {
            $userData['groups'] = array();
        }

        $this->groups = new GroupCollection($userData['groups']);
        unset($userData['groups']);

        foreach($userData as $name => $data)
        {
            if(in_array($name, User::$canonicalFields))
            {
                $this->basicData[$name] = $data;
                unset($userData[$name]);
            }
        }

        if(isset($userData['user_id']))
        {
            unset($userData['user_id']);
        }

        $this->customData = $userData;
    }

    /**
     * @return array
     * @see User::$canonicalFields
     */
    public function getBasicData()
    {
        return $this->basicData;
    }

    /**
     * @return array
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * @return GroupCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }
} 