<?php


namespace Anime\Model\News;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class NewsTagsManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'news_tags';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $newsId
     *
     * @return array
     */
    public function findForNews($newsId)
    {
        return $this->database->
            select('t.*')->
            from(array('t' => $this->table))->
            join('news_with_tags', 'c.tag_id=t.id', 'c')->
            where(array('news_id' => $newsId))->
            get()->
            fetchAll();
    }

    public function create(array $data = array())
    {
        return new NewsTags($data);
    }
} 