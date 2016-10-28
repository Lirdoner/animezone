<?php


namespace Anime\Model\News;


use Sequence\Database\Entity\BaseEntity;

class NewsWithTag extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  int */
    protected $tag_id;

    /** @var  int */
    protected $news_id;

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
     * @param int $tags_id
     */
    public function setTagId($tags_id)
    {
        $this->tag_id = $tags_id;
    }

    /**
     * @return int
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * @param int $news_id
     */
    public function setNewsId($news_id)
    {
        $this->news_id = $news_id;
    }

    /**
     * @return int
     */
    public function getNewsId()
    {
        return $this->news_id;
    }
} 