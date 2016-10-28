<?php


namespace Sequence\Command;


use Sequence\Cache\Cache;
use Sequence\Routing\Router;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if(!$this->getContainer()->has('cache'))
        {
            return false;
        }

        $cache = $this->getContainer()->get('cache');

        if(!$cache instanceof Cache)
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
        $this->
            setName('cache:clear')->
            setDescription('Clears the cache')->
            addOption('group', null, InputArgument::OPTIONAL, 'name of group which you want to clear')->
            setHelp(<<<EOF
The <info>%command.name%</info> command clears the application cache. Optionally you can clear cache for specific group:

<info>php %command.full_name%</info>
<info>php %command.full_name% --group=name</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Sequence\Cache\Cache $cache */
        $cache = $this->getContainer()->get('cache');

        if($group = $input->getOption('group'))
        {
            if('router' == $group)
            {
                $this->clearRouterCache();
            } else
            {
                $cache->deleteGroup($group);
            }
        } else
        {
            //delete all
            $cache->deleteAll();
            $this->clearRouterCache();
        }

        $output->writeln('<info>Application cache cleared!</info>');

        return 0;
    }

    private function clearRouterCache()
    {
        if($this->getContainer()->has('router'))
        {
            $router = $this->getContainer()->get('router');

            if($router instanceof Router)
            {
                $router->clearCache();
            }
        }
    }
} 