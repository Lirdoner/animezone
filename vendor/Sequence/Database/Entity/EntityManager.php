<?php


namespace Sequence\Database\Entity;


use Sequence\Database\Database;

abstract class EntityManager
{
    /**
     * @var \Sequence\Database\Database
     */
    protected $database;

    protected $table;
    protected $index;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Finds an record by its primary key / identifier.
     *
     * @param $index
     *
     * @return mixed
     */
    public function find($index)
    {
        return $this->findOneBy(array($this->index => $index));
    }

    /**
     * Finds all records in the database.
     *
     * @param null|string $order
     *
     * @return array
     */
    public function findAll($order = null)
    {
        return $this->findBy(array(), $order);
    }

    /**
     * Finds records by a set of criteria.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        $this->database->select()->from($this->table);

        if(!empty($criteria))
        {
            $this->database->where($criteria);
        }

        if(null !== $orderBy)
        {
            $this->database->order($orderBy);
        }

        if(null !== $limit)
        {
            $this->database->limit($limit);
        }

        if(null !== $offset)
        {
            $this->database->offset($offset);
        }

        return $this->database->get()->fetchAll();
    }

    /**
     * Finds a record entity by a set of criteria.
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findOneBy(array $criteria)
    {
        return $this->database->select()->from($this->table)->where($criteria)->limit(1)->get()->fetch();
    }

    /**
     * Delete record by its primary key / identifier.
     *
     * @param EntityInterface $model
     *
     * @return \PDOStatement
     * @throws \InvalidArgumentException
     */
    public function delete(EntityInterface $model)
    {
        $data = $model->toArray();

        if(!array_key_exists($this->index, $data))
        {
            throw new \InvalidArgumentException(sprintf('Missing primary key "%s" in "%s".', $this->index, get_class($model)));
        }

        return $this->database->delete($this->table)->where(array($this->index => $data[$this->index]))->get();
    }

    /**
     * @param array $criteria
     *
     * @return \PDOStatement
     */
    public function deleteWhere(array $criteria)
    {
        return $this->database->delete($this->table)->where($criteria)->get();
    }

    /**
     * Update existing record, or create new if record does not exist.
     *
     * @param EntityInterface $model
     *
     * @return \PDOStatement
     * @throws \InvalidArgumentException
     */
    public function update(EntityInterface $model)
    {
        $data = $model->toArray();

        if(!array_key_exists($this->index, $data))
        {
            throw new \InvalidArgumentException(sprintf('Missing primary key "%s" in "%s".', $this->index, get_class($model)));
        }

        if(!isset($data[$this->index]) && false == $this->find($data[$this->index]))
        {
            return $this->database->insert($this->table, $data)->get();
        } else
        {
            return $this->database->update($this->table, array_filter($data, 'strlen'))->where(array($this->index => $data[$this->index]))->get();
        }
    }
} 