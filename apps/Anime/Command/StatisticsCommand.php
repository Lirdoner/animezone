<?php


namespace Anime\Command;


use Sequence\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatisticsCommand extends ContainerAwareCommand
{
    /** @var  \Sequence\Database\Database */
    private $database;

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if(!$this->getContainer()->has('database'))
        {
            return false;
        }

        $this->database = $this->getContainer()->get('database');

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('statistics')
            ->setDescription('Fix\'s incorrectly calculated statistics.')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of statistics which do you want to fix (available: "category" and "users").')
            ->setHelp('<info>php %command.full_name% --type=category</info>');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getOption('type');

        if(!in_array($type, array('users', 'category')))
        {
            $output->writeln(sprintf('<error>Unsupported type "%s". Available: "category" and "users".</error>', $type ?: 'empty'));

            return 50;
        }

        if('category' == $type)
        {
            $updated = $this->doCategory();
        } else
        {
            $updated = $this->doUsers();
        }

        $time = number_format(microtime(true) - $this->getContainer()->get('config')->framework->get('start_time'), 4);

        $output->writeln(sprintf('<info>Statistic for %s %s was fixed in %s seconds.</info>', $updated, $type, $time));

        return 0;
    }

    private function doCategory()
    {
        $categories = $this->database
            ->select()
            ->from('categories')
            ->get();

        $updated = 0;

        foreach($categories->fetchAll() as $category)
        {
            $toUpdate = array();

            $rating = $this->database
                ->select('CAST(AVG(value) AS DECIMAL(10,2)) as avg, COUNT(id) as count')
                ->from('rating')
                ->where(array('category_id' => $category['id']))
                ->get()->fetch();

            if($category['rating_avg'] !== $rating['avg'] && $rating['avg'])
            {
                $toUpdate['rating_avg'] = $rating['avg'];
            }

            if($category['rating_count'] !== $rating['count'])
            {
                $toUpdate['rating_count'] = $rating['count'];
            }

            $fans = $this->database
                ->select('COUNT(id) as count')
                ->from('favorites')
                ->where(array('category_id' => $category['id']))
                ->get()->fetch();

            if($category['fans'] !== $fans['count'])
            {
                $toUpdate['fans'] = $fans['count'];
            }

            $watching = $this->database
                ->select('type, COUNT(*) as count')
                ->from('watching')
                ->where(array('category_id' => $category['id']))
                ->group('type')
                ->get()->fetchAll();

            foreach($watching as $row)
            {
                if(1 == $row['type'] && $category['watching'] !== $row['count'])
                {
                    $toUpdate['watching'] = $row['count'];
                } elseif(2 == $row['type'] && $category['watched'] !== $row['count'])
                {
                    $toUpdate['watched'] = $row['count'];
                } elseif(3 == $row['type'] && $category['plans'] !== $row['count'])
                {
                    $toUpdate['plans'] = $row['count'];
                } elseif(4 == $row['type'] && $category['stopped'] !== $row['count'])
                {
                    $toUpdate['stopped'] = $row['count'];
                } elseif(5 == $row['type'] && $category['abandoned'] !== $row['count'])
                {
                    $toUpdate['abandoned'] = $row['count'];
                }
            }

            if(!empty($toUpdate))
            {
                $this->database->update('categories', $toUpdate)->where(array('id' => $category['id']))->get();
                $updated++;
            }
        }

        return $updated;
    }

    private function doUsers()
    {
        $updated = 0;

        $users = $this->database->select()->from('users_custom_field')->get();

        foreach($users->fetchAll() as $user)
        {
            $toUpdate = array();

            $favorites = $this->database
                ->select('COUNT(id) as count')
                ->from('favorites')
                ->where(array('user_id' => $user['user_id']))
                ->get()->fetch();

            if($user['favorites'] !== $favorites['count'])
            {
                $toUpdate['favorites'] = $favorites['count'];
            }

            $rated = $this->database
                ->select('COUNT(id) as count')
                ->from('rating')
                ->where(array('user_id' => $user['user_id']))
                ->get()->fetch();

            if($user['rated'] !== $rated['count'])
            {
                $toUpdate['rated'] = $rated['count'];
            }

            $commented = $this->database
                ->select('COUNT(id) as count')
                ->from('comments')
                ->where(array('user_id' => $user['user_id']))
                ->get()->fetch();

            if($user['commented'] !== $commented['count'])
            {
                $toUpdate['commented'] = $commented['count'];
            }

            $watching = $this->database
                ->select('type, COUNT(*) as count')
                ->from('watching')
                ->where(array('user_id' => $user['user_id']))
                ->group('type')
                ->get()->fetchAll();

            foreach($watching as $row)
            {
                if(1 == $row['type'] && $user['watching'] !== $row['count'])
                {
                    $toUpdate['watching'] = $row['count'];
                } elseif(2 == $row['type'] && $user['watched'] !== $row['count'])
                {
                    $toUpdate['watched'] = $row['count'];
                } elseif(3 == $row['type'] && $user['plans'] !== $row['count'])
                {
                    $toUpdate['plans'] = $row['count'];
                } elseif(4 == $row['type'] && $user['stopped'] !== $row['count'])
                {
                    $toUpdate['stopped'] = $row['count'];
                } elseif(5 == $row['type'] && $user['abandoned'] !== $row['count'])
                {
                    $toUpdate['abandoned'] = $row['count'];
                }
            }

            if(!empty($toUpdate))
            {
                $this->database->update('users_custom_field', $toUpdate)->where(array('user_id' => $user['user_id']))->get();
                $updated++;
            }
        }

        return $updated;
    }
} 