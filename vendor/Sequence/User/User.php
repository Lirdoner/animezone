<?php

namespace Sequence\User;


/**
 * Class User
 * @package Sequence\User
 */
class User
{
    /**
     * @var array an array with basic user fields:
     *  * id - unique ID,
     *  * name - unique nickname,
     *  * email - unique e-mail address,
     *  * password - hash of password,
     *  * last_login - date of last login,
     *  * enabled - 0 => disabled, 1 => enabled, 3 => blocked
     *  * role - basic role (ROLE_ADMIN, ROLE_USER or ROLE_GUEST),
     *  * ip -
     *  * date_created - date of created user
     */
    public static $canonicalFields = array(
        'id', 'name', 'email', 'password', 'last_login', 'enabled', 'role', 'ip', 'date_created'
    );

    /**
     * canonical roles
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_GUEST = 'ROLE_GUEST';

    protected $userData;
    protected $customData;
    protected $groups;
    protected $_roles;
    protected $_groupCollection;

    /**
     * @param array $userData
     */
    public function __construct(array $userData = array())
    {
        $time = new \DateTime();
        $dataParser = new UserDataParser(array_merge(array(
            'id' => 0,
            'name' => '',
            'email' => '',
            'password' => '',
            'last_login' => $time->format('Y-m-d H:i:s'),
            'enabled' => 1,
            'role' => self::ROLE_GUEST,
            'ip' => '',
            'date_created' => $time->format('Y-m-d H:i:s')
        ), $userData));

        $this->userData = $dataParser->getBasicData();
        $this->customData = $dataParser->getCustomData();
        $this->_groupCollection = $dataParser->getGroups();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->userData['id'];
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->userData['name'];
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->userData['email'];
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->userData['password'];
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return new \DateTime($this->userData['last_login']);
    }

    /**
     * @return int 0 => disabled, 1 => enabled, 3 => blocked
     */
    public function getEnabled()
    {
        return (int)$this->userData['enabled'];
    }

    public function isEnabled()
    {
        return 1 == (int)$this->userData['enabled'];
    }

    /**
     * @return string canonical role
     */
    public function getRole()
    {
        return $this->userData['role'];
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->userData['ip'];
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return new \DateTime($this->userData['date_created']);
    }


    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->hasRole(self::ROLE_USER);
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return $this->hasRole(self::ROLE_GUEST);
    }

    /**
     * @param $userName
     * @throws \InvalidArgumentException
     */
    public function setUsername($userName)
    {
        $this->userData['name'] = $userName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->userData['email'] = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword($password, $hash = true)
    {
        if($hash)
        {
            $this->userData['password'] = password_hash($password, PASSWORD_BCRYPT, array('cost' => 11, 'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)));
        } else
        {
            $this->userData['password'] = $password;
        }
    }

    /**
     * @param int $enabled 0 => disabled, 1 => enabled, 3 => blocked
     */
    public function setEnabled($enabled)
    {
        $this->userData['enabled'] = (int) $enabled;
    }


    /**
     * @param bool $boolean true if you want set
     */
    public function setAdmin($boolean)
    {
        if(true === $boolean)
        {
            $this->userData['role'] = self::ROLE_ADMIN;
        } else
        {
            $this->userData['role'] = self::ROLE_USER;
        }
    }

    /**
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->userData['last_login'] = $time->format('Y-m-d H:i:s');
    }

    /**
     * @param \DateTime $time
     */
    public function setDateCreated(\DateTime $time)
    {
        $this->userData['date_created'] = $time->format('Y-m-d H:i:s');
    }

    /**
     * @param $ip
     */
    public function setIp($ip)
    {
        $this->userData['ip'] = $ip;
    }

    /**
     * @param array $data
     * @see self::$canonicalFields
     */
    public function setUserData(array $data)
    {
        $this->userData = array_merge($this->userData, $data);
    }

    /**
     * @param array $data an array of custom fields where field => value
     */
    public function setCustomData(array $data)
    {
        $this->customData = array_merge($this->customData, $data);
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return empty($this->customData) ? false : $this->customData;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return array_merge($this->userData, array('groups' => $this->groups), $this->customData);
    }

    /**
     * @param $key
     * @param bool $default
     *
     * @return bool|mixed
     */
    public function getCustomField($key, $default = false)
    {
        return !empty($this->customData[$key]) ? $this->customData[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setCustomField($key, $value)
    {
        $this->customData[$key] = $value;
    }

    /**
     * @return array an array of all user roles (basic + roles from groups)
     */
    public function getRoles()
    {
        if(null === $this->_roles)
        {
            $this->_roles = array_merge(array($this->userData['role'] => 1), $this->_groupCollection->getRoles());
        }

        return $this->_roles;
    }

    /**
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = $this->getRoles();
        
        if(isset($roles[self::ROLE_ADMIN]))
        {
            return true;
        }
        
        if(is_array($role))
        {
            foreach($role as $row)
            {
                if(isset($roles[$row]))
                {
                    return true;
                }
            }
        } else
        {
            if(isset($roles[$role]))
            {
                return true;
            }
        }
        
        return false;
	}

    /**
     * @param $name
     * @return bool
     */
    public function hasGroup($name)
    {
        if(null === $this->groups)
        {
            $this->getGroups();
        }

        return isset($this->groups[$name]);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if(null === $this->groups)
        {
            $this->groups = $this->_groupCollection->getGroups();
        }

        return $this->groups;
    }

    /**
     * @return array
     */
    public function getGroupNames()
    {
        return array_values($this->getGroups());
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->getUsername();
    }
}