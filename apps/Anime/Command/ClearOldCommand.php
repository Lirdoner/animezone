<?php


namespace Anime\Command;


use Anime\Model\User\UsersOnlineManager;
use Sequence\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOldCommand extends ContainerAwareCommand
{
    public function isEnabled()
    {
        if(!$this->getContainer()->has('database'))
        {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('old:clean')
            ->setDescription('Clears old rows');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Sequence\Database\Database $database */
        $database = $this->getContainer()->get('database');

        $usersOnline = new UsersOnlineManager($database);
        $usersOnline->clearOld();
        $output->writeln('<info>Old sessions was cleared!</info>');

        $database->delete('users')->where(array('enabled' => 0))->where('date_created < DATE_SUB(NOW(), INTERVAL 7 DAY)')->get();
        $output->writeln('<info>Not activated accounts was cleared!</info>');

        return 0;
    }
} 