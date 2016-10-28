<?php


namespace Anime\Model\Type;


use Sequence\Database\Entity\BaseEntity;

class TypeForCategory extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $category_id;

    /** @var  int */
    protected $type_id;

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
     * @param int $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }
} 