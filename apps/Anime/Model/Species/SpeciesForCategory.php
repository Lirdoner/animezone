<?php


namespace Anime\Model\Species;


use Sequence\Database\Entity\BaseEntity;

class SpeciesForCategory extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $category_id;

    /** @var  int */
    protected $species_id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param int $species_id
     */
    public function setSpeciesId($species_id)
    {
        $this->species_id = $species_id;
    }

    /**
     * @return int
     */
    public function getSpeciesId()
    {
        return $this->species_id;
    }
} 