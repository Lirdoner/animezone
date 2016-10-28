<?php


namespace Sequence\Provider;


use Sequence\Container;
use Sequence\ProviderInterface;
use Sequence\Routing\Router;
use Symfony\Component\HttpKernel\EventListener\RouterListener;

class RouterProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $router = new Router($container->get('cache'), $container->get('config'), $container->get('request_context'));

        if($container->get('config')->framework->debug)
        {
            $logger = $container->get('logger');
        } else
        {
            $logger = null;
        }

        $container->set('router', $router);
        $container->get('dispatcher')->addSubscriber(new RouterListener($router->getMatcher(), $container->get('request_context'), $logger, $container->get('request_stack')));
    }
} 