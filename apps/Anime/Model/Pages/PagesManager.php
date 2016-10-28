<?php


namespace Anime\Model\Pages;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class PagesManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'pages';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return Pages
     */
    public function create(array $data = array())
    {
        return new Pages($data);
    }
} 