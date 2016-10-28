<?php


namespace Anime\Model;


trait ListTrait
{
    /**
     * @param array $criteria
     * @param null|string $order
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return \Sequence\Database\Database
     */
    public function findListBy(array $criteria, $order = null, $limit = null, $offset = null)
    {
        $this->database->select()->from($this->table);

        if(!empty($criteria))
        {
            $this->database->where($criteria);
        }

        if(null !== $order)
        {
            $this->database->order($order);
        }

        if(null !== $limit)
        {
            $this->database->limit($limit);
        }

        if(null !== $offset)
        {
            $this->database->offset($offset);
        }

        return $this->database;
    }
} 