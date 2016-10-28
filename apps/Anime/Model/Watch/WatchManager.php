<?php


namespace Anime\Model\Watch;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class WatchManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'watching';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $userId
     * @param null|string $order
     * @param null|int $limit
     * @param int $type
     *
     * @return Database
     */
    public function findForUser($userId, $order = null, $limit = null, $type = Watch::WATCHING)
    {
        $this->database->
            select('c.name, c.alias, c.image, date')->
            from($this->table)->
            join('categories', 'category_id=c.id', 'c')->
            where(array('user_id' => $userId, 'type' => $type));

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
     * @param array $criteria
     *
     * @return int
     */
    public function exists(array $criteria)
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
     * @return Watch
     */
    public function create(array $data = array())
    {

        if(empty($data['date']))
        {
            $time = new \DateTime();

            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new Watch($data);
    }
} 