<?php


namespace Anime\Model\Episode;


use Sequence\Database\Entity\BaseEntity;

class Episode extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $category_id;

    /** @var  int */
    protected $number;

    /** @var  string */
    protected $title;

    /** @var  int */
    protected $enabled;

    /** @var  int */
    protected $filler;

    /** @var  string */
    protected $date_add;

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
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param int $status
     */
    public function setEnabled($status)
    {
        $this->enabled = $status;
    }

    /**
     * @return int
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param int $filler
     */
    public function setFiller($filler)
    {
        $this->filler = $filler;
    }

    /**
     * @return int
     */
    public function getFiller()
    {
        return $this->filler;
    }

    /**
     * @param \DateTime $date_add
     */
    public function setDateAdd(\DateTime $date_add)
    {
        $this->date_add = $date_add->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getDateAdd()
    {
        return $this->date_add;
    }
} 