<?php


namespace Anime\Model\Server;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class ServerManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'servers';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return Server
     */
    public function create(array $data = array())
    {
        return new Server($data);
    }
} 