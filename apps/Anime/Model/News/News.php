<?php


namespace Anime\Model\News;


use Sequence\Database\Entity\BaseEntity;

class News extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  string */
    protected $title;

    /** @var  string */
    protected $alias;

    /** @var  string */
    protected $description;

    /** @var  string */
    protected $image;

    /** @var  int */
    protected $comments;

    /** @var  int */
    protected $views;

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
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @param int $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return int
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime$date)
    {
        $this->date = $date->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
} 