<?php


namespace Anime\Model\Ads;


use Anime\Model\ListTrait;
use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class AdsManager extends EntityManager
{
    use ListTrait;

    /**
     * @var \Sequence\Cache\Cache
     */
    protected $cache;

    /**
     * @param Database $database
     * @param Cache $cache
     */
    public function __construct(Database $database, Cache $cache)
    {
        $this->table = 'ads';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @param string $alias
     *
     * @return array|mixed
     */
    public function findByAlias($alias)
    {
        if(null == $data = $this->cache->get('ads/'.$alias))
        {
            $data = $this->findBy(array('alias' => $alias));

            $this->cache->set('ads/'.$alias, $data);
        }

        return $data;
    }

    /**
     * @param string $type
     *
     *
     * @return array|string
     */
    public function random($type)
    {
        $data = $this->findByAlias($type);

        if(empty($data))
        {
            return false;
        }

        return $data[array_rand($data)]['code'];
    }

    /**
     * @return mixed
     */
    public function clearCache()
    {
        return $this->cache->deleteGroup('ads');
    }
} 