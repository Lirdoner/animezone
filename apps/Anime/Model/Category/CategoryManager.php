<?php


namespace Anime\Model\Category;


use Anime\Model\ListTrait;
use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class CategoryManager extends EntityManager
{
    use ListTrait;

    const UPDATE_FAVORITE = 'fans';
    const UPDATE_VIEWS = 'views';
    const UPDATE_RATING = 'rating_count';
    const UPDATE_WATCHING = 'watching';
    const UPDATE_WATCHED = 'watched';
    const UPDATE_PLANS = 'plans';
    const UPDATE_STOPPED = 'stopped';
    const UPDATE_ABANDONED = 'abandoned';

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
        $this->table = 'categories';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @param int $year
     *
     * @return mixed
     */
    public function getPrevSeason($year)
    {
        return $this->database->
            select('year')->
            from($this->table)->
            where(array('year <' => $year))->
            group('year')->
            order('year DESC')->
            limit(1)->
            get()->
            fetch();
    }

    /**
     * @param int $year
     *
     * @return mixed
     */
    public function getNextSeason($year)
    {
        return $this->database->
            select('year')->
            from($this->table)->
            where(array('year >' => $year))->
            group('year')->
            order('year ASC')->
            limit(1)->
            get()->
            fetch();
    }

    /**
     * @param array $searchBy
     * @param $type
     *
     * @return Database
     */
    public function search(array $searchBy, $type = Database::SQL_OR)
    {
        return $this->database->
            select()->
            from($this->table)->
            where($searchBy, $type);
    }

    /**
     * @return array|mixed
     */
    public function getSidebar()
    {
        if(null == $data = $this->cache->get('categories/sidebar'))
        {
            $data = $this->database->
                select(array('name', 'alias'))->
                from($this->table)->
                where(array('status' => Category::STATUS_EMITTED))->
                order('name ASC')->
                get()->
                fetchAll();

            $this->cache->set('categories/sidebar', $data);
        }

        return $data;
    }

    /**
     * @param int $limit
     *
     * @return array|mixed
     */
    public function getLastSeries($limit = 5)
    {
        if(null == $data = $this->cache->get('categories/newest'.$limit))
        {
            $data = $this->database->
                select(array('name', 'alias', 'image', 'description'))->
                from($this->table)->
                order('id DESC')->
                limit($limit)->
                get()->
                fetchAll();

            $this->cache->set('categories/newest'.$limit, $data);
        }

        return $data;
    }

    /**
     * @param Category $category
     * @param string $type
     * @param bool $increase
     *
     * @return \PDOStatement
     */
    public function updateOf(Category $category, $type = self::UPDATE_VIEWS, $increase = true)
    {
        return $this->database->query('UPDATE `'.$this->table.'` SET `'.$type.'`=`'.$type.'`'.($increase ? '+' : '-').'1 WHERE `id`='.$category->getId());
    }

    /**
     * @param int $categoryId
     * @param int $limit
     *
     * @return array
     */
    public function getSimilar($categoryId, $limit = 15)
    {
        if(null === $data = $this->cache->get('categories/similar.'.$categoryId.$limit))
        {
            $data = $this->database->query('
                SELECT c.name, c.alias FROM categories AS c
                JOIN topics_for_category AS t ON t.category_id=c.id
                WHERE t.topics_id IN (SELECT topics_id FROM topics_for_category WHERE category_id='.$categoryId.') AND NOT c.id='.$categoryId.'
                LIMIT '.$limit.'
            ');

            $data = $data->fetchAll();

            usort($data, function($a, $b){
                return strcmp($a['alias'], $b['alias']);
            });

            $this->cache->set('categories/similar.'.$categoryId.$limit, $data, 86400);
        }

        return $data;
    }

    /**
     * @param int $seriesId
     * @param int $limit
     *
     * @return array
     */
    public function getSeries($seriesId, $limit = 15)
    {
        if(null === $data = $this->cache->get('categories/series.'.$seriesId.$limit))
        {
            $data = $this->database->
                select(array('name', 'alias'))->
                from($this->table)->
                where(array('series' => $seriesId))->
                limit($limit)->
                order('name')->
                get()->fetchAll();

            $this->cache->set('categories/series.'.$seriesId.$limit, $data, 86400);
        }

        return $data;
    }

    /**
     * @param array $condition
     * @param null|string $order
     *
     * @return Database
     */
    public function getSpeciesBy(array $condition, $order = null)
    {
        $this->database->
            select('c.*')->
            from(array('c' => $this->table));

        if(isset($condition['types']))
        {
            $this->database->
                join('type_for_category', 'tc.category_id=c.id', 'tc')->
                andWhere(array('type_id' => $condition['types']));
        }

        if(isset($condition['species']))
        {
            $this->database->join('species_for_category', 'sc.category_id=c.id', 'sc')->
                andWhere(array('species_id' => $condition['species']));
        }

        if(isset($condition['topics']))
        {
            $this->database->join('topics_for_category', 'toc.category_id=c.id', 'toc')->
                andWhere(array('topics_id' => $condition['topics']));
        }

        $this->database->group('c.id');

        if(null !== $order)
        {
            $this->database->order($order);
        }

        return $this->database;
    }

    /**
     * @return array
     */
    public function getStats()
    {
        if(null === $data = $this->cache->get('categories/stats'))
        {
            $data = $this->database->query('
                SELECT
                SUM(if(`status`=0, 1, 0)) AS `emited`,
                SUM(if(`status`=1, 1, 0)) AS `ended`,
                SUM(if(`status`=2, 1, 0)) AS `coming`,
                SUM(if(`status`=3, 1, 0)) AS `rended`,
                SUM(if(`release`=1, 1, 0)) AS `movie`,
                SUM(if(`release`=2, 1, 0)) AS `ova`,
                SUM(if(`release`=3, 1, 0)) AS `tv`,
                COUNT(`id`) AS `all`,
                SUM(`views`) AS `views`
                FROM '.$this->table
            );

            $data = $data->fetch();

            $this->cache->set('categories/stats', $data, 86400);
        }

        return $data;
    }

    /**
     * @param int $seriesID
     * @return \PDOStatement
     */
    public function clearSeries($seriesID)
    {
        return $this->database->update($this->table, array('series' => 0))->where(array('series' => $seriesID))->get();
    }

    /**
     * @return bool
     */
    public function clearCache()
    {
        return $this->cache->deleteGroup('categories');
    }

    /**
     * @param array $data
     *
     * @return Category
     */
    public function create(array $data =  array())
    {
        return new Category($data);
    }
} 