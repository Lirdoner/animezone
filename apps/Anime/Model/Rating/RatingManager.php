<?php


namespace Anime\Model\Rating;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class RatingManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'rating';
        $this->index = 'id';
        parent::__construct($database);
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
            select('c.name, c.alias, c.image, value, date')->
            from($this->table)->
            join('categories', 'c.id=category_id', 'c')->
            where(array('user_id' => $userId));

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
     * @param int $userId
     *
     * @return int
     */
    public function countForUser($userId)
    {
        $this->database->
            select('id')->
            from($this->table)->
            where(array('user_id' => $userId));

        return $this->database->get()->rowCount();
    }

    /**
     * @param array $criteria
     * @param string|array $fields]
     *
     * @return mixed
     */
    public function findRatingBy(array $criteria, $fields = null)
    {
        return $this->database->
            select($fields)->
            from($this->table)->
            where($criteria)->
            get()->fetch();
    }

    /**
     * @param int $categoryId
     *
     * @return \PDOStatement
     */
    public function updateCategoryAvg($categoryId)
    {
        return $this->database->query('
            UPDATE `categories`
            SET `rating_avg`=(SELECT AVG(`value`) FROM `'.$this->table.'` WHERE `category_id`='.$categoryId.')
            WHERE `id`='.$categoryId
        );
    }

    /**
     * @param array $data
     *
     * @return Rating
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $time = new \DateTime();
            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new Rating($data);
    }
} 