<?php


namespace Anime\Model\Topics;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class TopicsForCategoryManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'topics_for_category';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return TopicsForCategory
     */
    public function create(array $data = array())
    {
        return new TopicsForCategory($data);
    }
} 