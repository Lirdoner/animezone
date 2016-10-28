<?php


namespace Anime\Model\Comment;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class CommentManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'comments';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array|string $criteria
     * @param null|string $orderBy
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return array
     */
    public function findListBy($criteria, $orderBy = null, $limit = null, $offset = null)
    {
        $this->database->
            select(array('k.*', 'u.name', 'u.role', 'uc.avatar'))->
            from(array('k' => $this->table))->
            join('users', 'u.id=k.user_id', 'u')->
            join('users_custom_field', 'uc.user_id=u.id', 'uc')->
            where($criteria);

        if(null !== $orderBy)
        {
            $this->database->order($orderBy);
        }

        if(null !== $limit)
        {
            $this->database->limit($limit);
        }

        if(null !== $offset)
        {
            $this->database->offset($offset);
        }

        return $this->database->get()->fetchAll();
    }

    /**
     * @param int $userId
     * @param null|string $order
     * @param null|int $limit
     *
     * @return Database
     */
    public function findForUser($userId, $order = null, $limit = null)
    {
        $this->database->
            select(array('k.*', 'u.name', 'u.role', 'uc.avatar'))->
            from(array('k' => $this->table))->
            join('users', 'u.id=k.user_id', 'u')->
            join('users_custom_field', 'uc.user_id=u.id', 'uc')->
            where('k.user_id='.$userId);

        if(null !== $order)
        {
            $this->database->order($order);
        }

        if(null !== $limit)
        {
            $this->database->limit($limit);
        }

        return $this->database;
    }

    /**
     * @return \Sequence\Database\Database
     */
    public function findList()
    {
        return $this->database->select()->from($this->table);
    }

    /**
     * @param string|array $criteria
     *
     * @return int
     */
    public function count($criteria)
    {
        return $this->database->
            select()->
            from($this->table)->
            where($criteria)->
            get()->
            rowCount();
    }

    /**
     * @param array $data
     *
     * @return Comment
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $time = new \DateTime();
            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new Comment($data);
    }
} 