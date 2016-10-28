<?php


namespace Sequence\User;


/**
 * Class Group
 * @package Sequence\User
 */
class Group
{
    protected $name;
    protected $oldName;
    protected $roles;
    protected $real_name;
    protected $description;

    /**
     * @param    array $values
     * @throws   \InvalidArgumentException
     */
    public function __construct(array $values = array())
    {
        if(!isset($values['name']))
        {
            throw new \InvalidArgumentException('Missing parameter "name", unique name of group (e.g. ID).');
        }

        $this->setName($values['name']);
        $this->oldName = $values['name'];

        if(isset($values['roles']))
        {
            if(!is_array($values['roles']))
            {
                $values['roles'] = explode(',', $values['roles']);
            }

            $this->roles = array();

            foreach($values['roles'] as $role)
            {
                $this->roles[trim($role)] = 1;
            }
        }

        if(isset($values['real_name']))
        {
            $this->real_name = $values['real_name'];
        }

        if(isset($values['description']))
        {
            $this->description = $values['description'];
        }
    }

    /**
     * @param string $role
     * @throws \InvalidArgumentException
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if($this->hasRole($role))
        {
            throw new \InvalidArgumentException(sprintf('That role "%s" already exists.', $role));
        }

        $this->roles[$role] = 1;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return isset($this->roles[$role]);
    }

    /**
     * @param bool $string
     * @return array|string
     */
    public function getRoles($string = false)
    {
        return $string ? implode(',', array_keys($this->roles)) : $this->roles;
    }

    /**
     * @return string|null
     */
    public function getRealName()
    {
        return $this->real_name;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $role
     * @throws \InvalidArgumentException
     */
    public function removeRole($role)
    {
        if(!$this->hasRole($role))
        {
            throw new \InvalidArgumentException(sprintf('That role "%s" does not exist.', $role));
        }

        unset($this->roles[$role]);
    }

    /**
     * @param $name
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        if(!preg_match('#[a-z0-9-_]+#i', $name))
        {
            throw new \InvalidArgumentException(sprintf('Unsupported type of "%s" name. Supported only "a-z, 0-9, -, _"', $name));
        }

        $this->name = $name;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = array_map('strtoupper', $roles);
    }

    /**
     * @param $real_name
     * @return mixed
     */
    public function setRealName($real_name)
    {
        return $this->real_name = $real_name;
    }

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description)
    {
        return $this->description = $description;
    }
}