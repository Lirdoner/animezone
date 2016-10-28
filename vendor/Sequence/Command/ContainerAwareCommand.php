<?php


namespace Sequence\Command;


use Sequence\Container;
use Symfony\Component\Console\Command\Command;

abstract class ContainerAwareCommand extends Command
{
    /**
     * @var \Sequence\Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
} 