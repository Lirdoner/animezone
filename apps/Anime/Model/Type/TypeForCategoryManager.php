<?php


namespace Anime\Model\Type;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class TypeForCategoryManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'type_for_category';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return TypeForCategory
     */
    public function create(array $data = array())
    {
        return new TypeForCategory($data);
    }
} 