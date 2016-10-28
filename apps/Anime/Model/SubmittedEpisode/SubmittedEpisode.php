<?php


namespace Anime\Model\SubmittedEpisode;


use Sequence\Database\Entity\BaseEntity;

class SubmittedEpisode extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  string */
    protected $title;

    /** @var  string */
    protected $links;

    /** @var  string */
    protected $ip;

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
     * @param string $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param \DateTime $date
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