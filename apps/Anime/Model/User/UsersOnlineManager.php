<?php


namespace Anime\Model\User;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class UsersOnlineManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'users_online';
        $this->index = 'sess_id';
        parent::__construct($database);
    }

    /**
     * @return \Sequence\Database\Database
     */
    public function findList()
    {
        $this->database->
            select()->
            from(array('s' => $this->table))->
            leftJoin('users', 'u.id=s.user_id', 'u');

        return $this->database;
    }

    /**
     * @param string $interval
     *
     * @return \PDOStatement
     */
    public function clearOld($interval = '30 DAY')
    {
        return $this->database->
            delete($this->table)->
            where('last_active < DATE_SUB(NOW(), INTERVAL '.$interval.')')->
            get();
    }
} 