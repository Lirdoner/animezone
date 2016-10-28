<?php


namespace Sequence\Provider;


use Sequence\Container;
use Sequence\ProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $options = array_replace_recursive(array(
            'storage_class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\NativeSessionStorage',
            'name' => '_SESS',
        ), isset($options['options']) ? $options['options'] : array());

        if(!empty($options['storage_key']) && !empty($options['attributes_class']) && !empty($options['flashes_class']))
        {
            $attributes = new $options['attributes_class']($options['storage_key']);
            $flashes = new $options['flashes_class']($options['storage_key']);;
        } else
        {
            $attributes = null;
            $flashes = null;
        }


        $session = new Session(new $options['storage_class']($options), $attributes, $flashes);
        $session->start();

        //$container->set('session', $session);
        $container->get('request')->setSession($session);
    }
} 