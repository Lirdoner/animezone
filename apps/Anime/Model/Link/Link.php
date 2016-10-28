<?php


namespace Anime\Model\Link;


use Sequence\Database\Entity\BaseEntity;

class Link extends BaseEntity
{
    const LANG_JP = 'JP';
    const LANG_ID_JP = 0;
    const LANG_EN = 'EN';
    const LANG_ID_EN = 1;
    const LANG_PL = 'PL';
    const LANG_ID_PL = 2;

    /** @var  int */
    protected $id;

    /** @var  int */
    protected $episode_id;

    /** @var  int */
    protected $server_id;

    /** @var  string */
    protected $lang;

    /** @var  int */
    protected $lang_id;

    /** @var array  */
    protected $languages = array(
        self::LANG_ID_JP => self::LANG_JP,
        self::LANG_ID_EN => self::LANG_EN,
        self::LANG_ID_PL => self::LANG_PL,
    );

    /** @var  string */
    protected $code;

    /** @var  string */
    protected $info;

    protected $guarded = array('languages');

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
     * @param int $episode_id
     */
    public function setEpisodeId($episode_id)
    {
        $this->episode_id = $episode_id;
    }

    /**
     * @return int
     */
    public function getEpisodeId()
    {
        return $this->episode_id;
    }

    /**
     * @param int $server_id
     */
    public function setServerId($server_id)
    {
        $this->server_id = $server_id;
    }

    /**
     * @return int
     */
    public function getServerId()
    {
        return $this->server_id;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param int $lang_id
     */
    public function setLangId($lang_id)
    {
        $this->lang_id = $lang_id;
    }

    /**
     * @return int
     */
    public function getLangId()
    {
        return $this->lang_id;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $postscript
     */
    public function setInfo($postscript)
    {
        $this->info = $postscript;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }
} 