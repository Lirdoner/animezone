<?php


namespace Anime\Model\Link;


use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityInterface;
use Sequence\Database\Entity\EntityManager;

class LinkManager extends EntityManager
{
    /**
     * @var \Sequence\Cache\Cache
     */
    protected $cache;

    /**
     * @param Database $database
     * @param Cache $cache
     */
    public function __construct(Database $database, Cache $cache = null)
    {
        $this->table = 'links';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @param null|string $select
     * @return Database
     */
    public function findList($select = null)
    {
        return $this->database->
            select($select ?: 'l.*, s.name, s.mobile')->
            from(array('l' => $this->table))->
            join('servers', 's.id=l.server_id', 's');
    }

    /**
     * @param array $criteria
     * @param null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return \PDOStatement
     */
    public function findLinksBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        $this->findList()->where($criteria);

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

        return $this->database->get();
    }

    /**
     * @return array
     */
    public function getStats()
    {
        if(null === $data = $this->cache->get('links/stats'))
        {
            $data = $this->database->query('
                SELECT
                SUM(if(`lang_id`=0, 1, 0)) AS jp,
                SUM(if(`lang_id`=1, 1, 0)) AS en,
                SUM(if(`lang_id`=2, 1, 0)) AS pl,
                COUNT(`id`) AS `all`
                FROM '.$this->table
            );

            $data = $data->fetch();

            $this->cache->set('links/stats', $data, 86400);
        }

        return $data;
    }

    /**
     * @param int $linkId
     *
     * @return array|false
     */
    public function findCategory($linkId)
    {
        return $this->database->select('e.number, l.server_id, l.lang, c.alias')->
            from(array('l' => $this->table))->
            join('episodes', 'e.id=l.episode_id', 'e')->
            join('categories', 'c.id=e.category_id', 'c')->
            where('l.id='.$linkId)->
            get()->
            fetch();
    }

    /**
     * @return mixed
     */
    public function clearCache()
    {
        return $this->cache->deleteGroup('links');
    }

    /**
     * @param array $data
     *
     * @return Link
     */
    public function create(array $data = array())
    {
        return new Link($data);
    }

    /**
     * @param EntityInterface $model
     *
     * @return \PDOStatement
     */
    public function update(EntityInterface $model)
    {
        $data = $model->toArray();

        if(!array_key_exists($this->index, $data))
        {
            throw new \InvalidArgumentException(sprintf('Missing primary key "%s" in "%s".', $this->index, get_class($model)));
        }

        if(!isset($data[$this->index]) && false == $this->find($data[$this->index]))
        {
            return $this->database->insert($this->table, $data)->get();
        } else
        {
            return $this->database->update($this->table, $data)->where(array($this->index => $data[$this->index]))->get();
        }
    }
} 