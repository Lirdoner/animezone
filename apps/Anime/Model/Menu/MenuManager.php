<?php


namespace Anime\Model\Menu;


use Anime\Model\ListTrait;
use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class MenuManager extends EntityManager
{
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
        $this->table = 'menu';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @return array|mixed
     */
    public function getAll()
    {
        if(null == $data = $this->cache->get('menu'))
        {
            $data = $this->getSorted();

            $this->cache->set('menu', $data);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSorted()
    {
        $data = array();

        $menu = $this->findBy(array('parent_id' => 0), 'position ASC');

        foreach($menu as $i => $item)
        {
            $data[$item['id']] = $item;
            $data[$item['id']]['submenu'] = $this->findBy(array('parent_id' => $item['id']), 'position ASC');
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Menu
     */
    public function create(array $data = array())
    {
        if(isset($data['parent_id']) && $data['parent_id'] > 0)
        {
            $data['position'] = $this->lastPosition($data['parent_id']) + 1;
        }

        if(empty($data['position']))
        {
            $data['position'] = $this->lastPosition() + 1;
        }

        return new Menu($data);
    }

    /**
     * @return bool
     */
    public function clearCache()
    {
        return $this->cache->delete('menu');
    }

    /**
     * @param int $parent
     * @return int
     */
    protected function lastPosition($parent = 0)
    {
        return $this->database->
            select()->
            from($this->table)->
            where(array('parent_id' => $parent))->
            get()->
            rowCount();
    }
} 