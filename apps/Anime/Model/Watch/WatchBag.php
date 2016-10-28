<?php


namespace Anime\Model\Watch;


class WatchBag 
{
    /** @var  array */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setAll($data);
    }

    /**
     * @param int $category
     *
     * @return bool
     */
    public function get($category)
    {
        return array_key_exists($category, $this->data) ? $this->data[$category] : false;
    }

    /**
     * @param int $category
     * @param int $type
     */
    public function set($category, $type = Watch::WATCHING)
    {
        $this->data[$category] = $type;
    }

    /**
     * @param int $category
     * @param int $type
     *
     * @return bool
     */
    public function has($category, $type = Watch::WATCHING)
    {
        return array_key_exists($category, $this->data) && $this->data[$category] == $type;
    }

    /**
     * @param array $data
     */
    public function setAll(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }
} 