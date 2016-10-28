<?php


namespace Anime\Model\Report;


use Sequence\Database\Entity\BaseEntity;

class Report extends BaseEntity
{
    const TYPE_LINK = 1;
    const TYPE_COMMENT = 2;
    const TYPE_CONTACT = 3;

    /** @var  int */
    protected $id;

    /** @var  string */
    protected $type;

    /** @var  string */
    protected $content;

    /** @var  string */
    protected $mail;

    /** @var  string */
    protected $subject;

    /** @var  int */
    protected $link_id;

    /** @var  string */
    protected $report_ip;

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
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param int $link_id
     */
    public function setLinkId($link_id)
    {
        $this->link_id = $link_id;
    }

    /**
     * @return int
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /**
     * @param string $report_ip
     */
    public function setReportIp($report_ip)
    {
        $this->report_ip = $report_ip;
    }

    /**
     * @return string
     */
    public function getReportIp()
    {
        return $this->report_ip;
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