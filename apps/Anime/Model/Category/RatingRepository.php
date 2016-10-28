<?php


namespace Anime\Model\Category;


use Symfony\Component\HttpFoundation\Request;

class RatingRepository
{
    const ASCEND = 'down';
    const DESCEND = 'up';

    /** @var  \Symfony\Component\HttpFoundation\ParameterBag */
    private $query;

    /** @var  RatingEntity */
    private $current;

    /** @var RatingEntity[]  */
    private $collection = array();

    public function __construct(Request $request)
    {
        $this->query = $request->query;
    }

    /**
     * @param RatingEntity $entity
     *
     * @throws \InvalidArgumentException
     */
    public function add(RatingEntity $entity)
    {
        if(null == $entity->getColumn())
        {
            throw new \InvalidArgumentException('Entity can not be empty.');
        }

        $this->collection[$entity->getColumn()] = $entity;
    }

    /**
     * @param string $name
     *
     * @return RatingEntity
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if(!isset($this->collection[$name]))
        {
            throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist.', $name));
        }

        return $this->collection[$name];
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function setCurrent($name)
    {
        if(!isset($this->collection[$name]))
        {
            throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist.', $name));
        }

        $this->current = $this->collection[$name];
    }

    /**
     * @return RatingEntity
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param $name
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isCurrent($name)
    {
        if(!isset($this->collection[$name]))
        {
            throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist.', $name));
        }

        return $this->collection[$name] == $this->current;
    }

    /**
     * @param null|string $name
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getOrder($name = null)
    {
        if(null == $name)
        {
            return self::DESCEND !== $this->query->get($this->current->getColumn()) ? self::DESCEND : self::ASCEND;
        } elseif('readable' == $name)
        {
            return $this->query->get($this->current->getColumn());
        } elseif('sql' == $name)
        {
            return self::DESCEND == $this->query->get($this->current->getColumn()) ? 'DESC' : 'ASC';
        } elseif('sql_desc' == $name)
        {
            return self::DESCEND == $this->query->get($this->current->getColumn()) ? 'ASC' : 'DESC';
        } else
        {
            if(isset($this->collection[$name]))
            {
                return self::DESCEND !== $this->query->get($this->collection[$name]->getColumn()) ? self::DESCEND : self::ASCEND;
            } else
            {
                throw new \InvalidArgumentException(sprintf('Entity "%s" does not exist.', $name));
            }
        }
    }
} 