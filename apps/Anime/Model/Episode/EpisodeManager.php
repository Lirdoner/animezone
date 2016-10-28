<?php


namespace Anime\Model\Episode;


use Anime\Model\ListTrait;
use Sequence\Cache\Cache;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class EpisodeManager extends EntityManager
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
        $this->table = 'episodes';
        $this->index = 'id';
        $this->cache = $cache;
        parent::__construct($database);
    }

    /**
     * @return \Sequence\Database\Database
     */
    public function findList()
    {
        return $this->database->
            select('e.*, c.name, c.alias')->
            from(array('e' => $this->table))->
            join('categories', 'e.category_id=c.id', 'c');
    }

    /**
     * @param $categoryId
     *
     * @return \PDOStatement|false
     */
    public function getEpisodesForCategory($categoryId)
    {
        return $this->database->query('
            SELECT *
            FROM
            (
                SELECT e.*, c.name, c.alias, l.lang
                FROM episodes AS e
                JOIN categories AS c ON c.id = e.category_id
                JOIN links AS l ON e.id = l.episode_id
                WHERE c.id = '.$categoryId.'
                ORDER BY FIELD(l.lang_id, 2, 1, 0)
            ) AS query
            GROUP BY number
            ORDER BY number DESC
        ');
    }

    /**
     * @param int    $limit
     * @param bool   $all
     * @param string $interval
     *
     * @return array|mixed|null
     */
    public function getLatest($limit = 60, $all = true, $interval = '3 MONTH')
    {
        if(null == $data = $this->cache->get('episodes/latest'.$limit.($all ? 'all' : 'pl')))
        {
            $data = $this->database->query('
                SELECT *
                FROM
                (
                    SELECT e.date_add, e.number, l.lang, e.title, c.name, c.alias, c.image, e.category_id, c.id'.($limit <= 15 ? ', c.description, c.alternate, c.pegi, c.year, c.status' : null).'
                    FROM episodes AS e
                    JOIN categories AS c ON e.category_id = c.id
                    JOIN links AS l ON l.episode_id=e.id
                    WHERE e.enabled = 1 AND e.date_add > NOW() - INTERVAL '.$interval.'
                    '.($all ? null : 'AND l.lang_id = 2').'
                    ORDER BY e.date_add DESC'.($all ? ', FIELD(l.lang_id, 2, 1, 0)' : null).'
                ) AS query
                GROUP BY category_id
                ORDER BY date_add DESC
                LIMIT '.$limit.'
            ');

            $data = $data->fetchAll();

            $this->cache->set('episodes/latest'.$limit.($all ? 'all' : 'pl'), $data);
        }

        return $data;
    }

    /**
     * @param int $categoryId
     * @param int $episode
     * @return array
     */
    public function getNeighbours($categoryId, $episode)
    {
        $result = $this->database->query('
            (SELECT `number`, "prev" kol FROM `'.$this->table.'` WHERE `category_id`='.$categoryId.' AND `number` < '.$episode.' ORDER BY `number` DESC LIMIT 1)
            UNION
            (SELECT `number`, "next" kol FROM `'.$this->table.'` WHERE `category_id`='.$categoryId.' AND `number` > '.$episode.' ORDER BY `number` ASC LIMIT 1)
        ');

        $neighbours = array();

        foreach($result->fetchAll() as $row)
        {
            if('next' == $row['kol'])
            {
                $neighbours['next'] = $row['number'];
            } elseif('prev' == $row['kol'])
            {
                $neighbours['prev'] = $row['number'];
            }
        }

        return $neighbours;
    }

    /**
     * @return array
     */
    public function getStats()
    {
        if(null === $data = $this->cache->get('episodes/stats'))
        {
            $data = $this->database->query('
                SELECT
                SUM(if(`enabled`=1, 1, 0)) AS active,
                SUM(if(`filler`=1, 1, 0)) AS filler,
                COUNT(`id`) AS `all`
                FROM '.$this->table
            );

            $data = $data->fetch();

            $this->cache->set('episodes/stats', $data, 86400);
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function clearCache()
    {
        return $this->cache->deleteGroup('episodes');
    }

    /**
     * @param array $data
     *
     * @return Episode
     */
    public function create(array $data = array())
    {
        if(empty($data['date_add']))
        {
            $time = new \DateTime();
            $data['date_add'] = $time->format('Y-m-d H:i:s');
        }

        return new Episode($data);
    }
} 