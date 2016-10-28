<?php


namespace Anime\Model\Topics;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class TopicsManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'topics';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getTopicsForCategory($categoryId)
    {
        return $this->database->
            select('t.id, t.name')->
            from(array('t' => $this->table))->
            leftJoin('topics_for_category', 'topics_id=t.id')->
            where(array('category_id' => $categoryId))->
            order('t.name')->
            get()->
            fetchAll();
    }

    /**
     * @param array $data
     *
     * @return Topics
     */
    public function create(array $data = array())
    {
        return new Topics($data);
    }
} 