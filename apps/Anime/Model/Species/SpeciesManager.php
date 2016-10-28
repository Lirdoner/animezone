<?php


namespace Anime\Model\Species;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class SpeciesManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'species';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    public function getSpeciesForCategory($categoryId)
    {
        return $this->database->
            select('s.id, s.name')->
            from(array('s' => $this->table))->
            join('species_for_category', 'species_id=s.id')->
            where(array('category_id' => $categoryId))->
            order('s.name')->
            get()->
            fetchAll();
    }

    /**
     * @param array $data
     *
     * @return Species
     */
    public function create(array $data = array())
    {
        return new Species($data);
    }
} 