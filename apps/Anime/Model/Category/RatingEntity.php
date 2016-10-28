<?php


namespace Anime\Model\Category;


class RatingEntity
{
    /** @var string  */
    private $column;
    /** @var string  */
    private $title;

    /**
     * @param null|string $column
     * @param null|string $title
     */
    public function __construct($column = null, $title = null)
    {
        $this->column = $column;
        $this->title = $title;
    }

    /**
     * @param string $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
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
} 