<?php


namespace Anime\Model\Species;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class SpeciesForCategoryManager extends EntityManager
{
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->index = 'id';
        $this->table = 'species_for_category';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return SpeciesForCategory
     */
    public function create(array $data = array())
    {
        return new SpeciesForCategory($data);
    }
} 