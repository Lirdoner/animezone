<?php


namespace Anime\Model\Faq;


use Sequence\Database\Entity\BaseEntity;

class Faq extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var string */
    protected $question;

    /** @var  string */
    protected $answer;

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
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }
} 