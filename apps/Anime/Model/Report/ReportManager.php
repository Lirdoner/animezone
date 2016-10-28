<?php


namespace Anime\Model\Report;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class ReportManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'reports';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return Report
     */
    public function create(array $data = array())
    {
        if(!isset($data['content']))
        {
            $data['content'] = '';
        }

        if(!isset($data['mail']))
        {
            $data['mail'] = '';
        }

        if(!isset($data['subject']))
        {
            $data['subject'] = '';
        }

        if(empty($data['date']))
        {
            $time = new \DateTime();
            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new Report($data);
    }
} 