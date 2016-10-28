<?php

namespace Sequence\Provider;


use Sequence\EventListener\TemplateListener;
use Sequence\GlobalVariables;
use Sequence\ProviderInterface;
use Sequence\Container;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;

class TemplatingProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        //add path for exception/error views
        $options['path'] = array_merge((array)$options['path'], array(10 => $container->get('config')->framework->framework_dir.'/Resources/views/%name%.php'));

        $loader = new FilesystemLoader($options['path']);

        if($container->get('config')->framework->debug)
        {
            $loader->setLogger($container->get('logger'));
        }

        $templating = new PhpEngine(new TemplateNameParser(), $loader);
        $templating->set(new SlotsHelper());
        $templating->addGlobal('app', new GlobalVariables($container));

        $container->set('templating', $templating);
        $container->get('dispatcher')->addSubscriber(new TemplateListener($templating));
    }
}