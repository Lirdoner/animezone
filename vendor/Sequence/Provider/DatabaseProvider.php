<?php

namespace Sequence\Provider;

use Sequence\ProviderInterface;
use Sequence\Container;
use Sequence\Database\DatabaseManager;

class DatabaseProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $container->set('database_manager', function() use ($options) {
            if(empty($options['connections']))
            {
                throw new \InvalidArgumentException('Connections list is empty or does not exist.');
            }

            return new DatabaseManager($options['connections']);
        });

        $container->set('database', function (Container $container) use ($options) {
            if(empty($options['default_connection']))
            {
                throw new \InvalidArgumentException('"default_connection" is not defined or is empty.');
            }

            return $container->get('database_manager')->getConnection($options['default_connection']);
        });
    }
} 