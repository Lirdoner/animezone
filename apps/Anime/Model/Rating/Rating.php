<?php


namespace Anime\Model\Rating;


use Sequence\Database\Entity\BaseEntity;

class Rating extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $user_id;

    /** @var  int */
    protected $category_id;

    /** @var  int */
    protected $value;

    /** @var  string */
    protected $date;

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
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
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
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \DateTime  $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
} 