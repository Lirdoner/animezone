<?php


namespace Anime\Model\Favorite;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class FavoriteManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'favorites';
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
            select('c.name, c.alias, c.image, date')->
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
    public function countByUser($userId)
    {
        return $this->countBy(array('user_id' => $userId));
    }

    /**
     * @param int $categoryId
     *
     * @return int
     */
    public function countByCategory($categoryId)
    {
        return $this->countBy(array('category_id' => $categoryId));
    }

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria)
    {
        return $this->database->select()->from($this->table)->where($criteria)->get()->rowCount();
    }

    /**
     * @param array $data
     *
     * @return Favorite
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $time = new \DateTime();
            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new Favorite($data);
    }
} 