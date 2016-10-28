<?php


namespace Anime\Model\Restore;


use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class RestoreManager extends EntityManager
{
    public function __construct(Database $database)
    {
        $this->table = 'users_restore';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param int $olderThan
     *
     * @return \PDOStatement
     */
    public function deleteOldCodes($olderThan = 1)
    {
        return $this->database->
            delete($this->table)->
            where('`date` < (NOW() - INTERVAL '.$olderThan.' DAY)')->
            get();
    }

    /**
     * @param array $data
     *
     * @return Restore
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $now = new \DateTime();

            $data['date'] = $now->format('Y-m-d H:i:s');
        }

        return new Restore($data);
    }
} 