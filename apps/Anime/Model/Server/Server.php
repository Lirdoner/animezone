<?php


namespace Anime\Model\Server;


use Sequence\Database\Entity\BaseEntity;

class Server extends BaseEntity
{
    /** @var  int */
    protected $id;

    /** @var  string */
    protected $name;

    /** @var  int */
    protected $mobile;

    /** @var  string */
    protected $template;

    /** @var  string */
    protected $templateSearchPattern = '{CODE}';

    protected $guarded = array('templateSearchPattern');

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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param int $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getHtml($code)
    {
        return str_replace($this->templateSearchPattern, $code, $this->getTemplate());
    }

    /**
     * @return string
     */
    public function getTemplateSearchPattern()
    {
        return $this->templateSearchPattern;
    }
} 