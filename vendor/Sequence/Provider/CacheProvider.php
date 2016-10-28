<?php

namespace Sequence\Provider;

use Sequence\ProviderInterface;
use Sequence\Container;
Use Sequence\Cache\Cache;

class CacheProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $container->set('cache', function (Container $container) use ($options) {
            if(!isset($options['driver']) && !isset($options['path']))
            {
                $options['path'] = $container->get('config')->framework->get('cache_dir');
                $options['driver'] = 'Sequence\\Cache\\Driver\\File';
            }

            if(!isset($options['prefix']))
            {
                $options['prefix'] = 'sequence.';
            }

            $driver = $options['driver'];

            $cache = new Cache();
            unset($options['enabled'], $options['driver']);

            $cache->setDriver(new $driver($options));

            return $cache;
        });
    }
}