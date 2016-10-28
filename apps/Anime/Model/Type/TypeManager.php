<?php


namespace Anime\Model\Type;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;


class TypeManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'type';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $categoryId
     *
     * @return mixed
     */
    public function getTypeForCategory($categoryId)
    {
        return $this->database->
            select('t.id, t.name')->
            from(array('t' => $this->table))->
            join('type_for_category', 'type_id=t.id')->
            where(array('category_id' => $categoryId))->
            get()->
            fetchAll();
    }

    /**
     * @param array $data
     *
     * @return Type
     */
    public function create(array $data = array())
    {
        return new Type($data);
    }
} 