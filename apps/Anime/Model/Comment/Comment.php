<?php


namespace Anime\Model\Comment;


use Sequence\Database\Entity\BaseEntity;

class Comment extends BaseEntity
{
    const TYPE_CATEGORY = 0;
    const TYPE_EPISODE = 1;
    const TYPE_NEWS = 2;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $to;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
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
     * @param int $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
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
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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