<?php


namespace Anime\Model\SubmittedEpisode;


use Anime\Model\ListTrait;
use Sequence\Database\Database;
use Sequence\Database\Entity\EntityManager;

class SubmittedEpisodeManager extends EntityManager
{
    use ListTrait;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->table = 'submitted_episode';
        $this->index = 'id';
        parent::__construct($database);
    }

    /**
     * @param array $data
     *
     * @return SubmittedEpisode
     */
    public function create(array $data = array())
    {
        if(empty($data['date']))
        {
            $time = new \DateTime();

            $data['date'] = $time->format('Y-m-d H:i:s');
        }

        return new SubmittedEpisode($data);
    }
} 