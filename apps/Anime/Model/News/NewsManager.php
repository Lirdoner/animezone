<?php


namespace Anime\Model\News;


use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class NewsManager extends EntityManager
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
        $this->table = 'news';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @return array|mixed
     */
    public function getLast()
    {
        if(null == $data = $this->cache->get('news/latest'))
        {
            $data = $this->database->
                select()->
                from($this->table)->
                where(array('date <=' => date('Y-m-d')))->
                order('date DESC')->
                limit(1)->
                get()->
                fetch();

            $this->cache->set('news/latest', $data, 86400);
        }

        return $data;
    }

    /**
     * @param int $limit
     *
     * @return array|mixed
     */
    public function getSidebar($limit = 10)
    {
        if(null == $data = $this->cache->get('news/last'.$limit))
        {
            $data = $this->findBy(array('date <=' => date('Y-m-d')), 'date DESC', $limit);

            $this->cache->set('news/last'.$limit, $data, 86400);
        }

        return $data;
    }

    /**
     * @param null|string $select
     *
     * @return Database
     */
    public function findList($select = null)
    {
        return $this->database->
            select($select)->
            from(array('n' => $this->table));
    }

    /**
     * @param array $data
     *
     * @return News
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $time = new \DateTime();
            $data['date'] = $time->format('Y-m-d');
        }

        return new News($data);
    }

    /**
     * @param News $news
     * @param bool $increase
     *
     * @return \PDOStatement
     */
    public function updateView(News $news, $increase = true)
    {
        return $this->database->query('UPDATE `'.$this->table.'` SET `views`=`views`'.($increase ? '+' : '-').'1 WHERE `id`='.$news->getId());
    }

    /**
     * @return mixed
     */
    public function clearCache()
    {
        return $this->cache->deleteGroup('news');
    }
} 