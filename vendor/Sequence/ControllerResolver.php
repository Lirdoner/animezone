<?php

namespace Sequence;


use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

/**
 * Class ControllerResolver
 * @package Sequence\Controller
 */
class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var \Sequence\Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        if($container->has('logger'))
        {
            parent::__construct($container->get('logger'));
        }
    }

    /**
     *
     *
     * @param $controller
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function parseControllerName($controller)
    {
        if(3 != count($parts = explode(':', $controller)))
        {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid "a:b:c" controller string.', $controller));
        }

        list($app, $controller, $action) = $parts;
        $class = $app.'\\Controller\\'.$controller.'Controller';

        return $class.'::'.$action.'Action';
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     *
     * @throws \LogicException When the name could not be parsed
     * @throws \InvalidArgumentException When the controller class does not exist
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::'))
        {
            $count = substr_count($controller, ':');
            if(2 == $count)
            {
                // controller in the a:b:c notation then
                $controller = $this->parseControllerName($controller);
            } else
            {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }

        list($class, $method) = explode('::', $controller, 2);

        if(!class_exists($class))
        {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $controller = new $class($this->container);

        return array($controller, $method);
    }
}