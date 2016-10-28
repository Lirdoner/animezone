<?php


namespace Anime\Model\Series;


use Anime\Model\ListTrait;
use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class SeriesManager extends EntityManager
{
    use ListTrait;

    /** @var  \Sequence\Cache\Cache */
    protected $cache;

    /**
     * @param Database $database
     * @param Cache $cache
     */
    public function __construct(Database $database, Cache $cache = null)
    {
        $this->cache = $cache;

        $this->table = 'series';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        if(null == $data = $this->cache->get('series.all'))
        {
            $data = $this->findBy(array(), 'name ASC');

            $this->cache->set('series.all', $data);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return Series
     */
    public function create(array $data = array())
    {
        return new Series($data);
    }

    /**
     * @return mixed
     */
    public function clearCache()
    {
        return $this->cache->delete('series.all');
    }
} 