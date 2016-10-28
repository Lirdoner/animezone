<?php


namespace Anime\Model\News;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class NewsWithTagsManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'news_with_tags';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     * @return NewsWithTag
     */
    public function create(array $data = array())
    {
        return new NewsWithTag($data);
    }
} 