<?php


namespace Anime\Model\Topics;


use Sequence\Database\Entity\BaseEntity;

class TopicsForCategory extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $category_id;

    /** @var  int */
    protected $topics_id;

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
     * @param int $topics_id
     */
    public function setTopicsId($topics_id)
    {
        $this->topics_id = $topics_id;
    }

    /**
     * @return int
     */
    public function getTopicsId()
    {
        return $this->topics_id;
    }
} 