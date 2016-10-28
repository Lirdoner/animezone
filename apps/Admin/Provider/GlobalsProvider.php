<?php


namespace Admin\Provider;


use Anime\Helper\TextHelper;
use Sequence\Cache\Cache;
use Sequence\Cache\Driver\File;
use Sequence\Container;
use Sequence\ProviderInterface;
use Symfony\Component\Templating\Helper\AssetsHelper;

class GlobalsProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        date_default_timezone_set('Europe/Warsaw');

        $container->get('templating')->set(new TextHelper());
        $container->get('templating')->set(new AssetsHelper($container->get('config')->anime->assets));

        $navigation = array();
        $navigation[] = array(
            'name' => 'Dashboard',
            'link' => '/',
            'icon' => 'fa fa-tachometer',
        );
        $navigation[] = array(
            'name' => 'Kategorie',
            'link' => '/categories',
            'icon' => 'fa fa-film',
        );
        $navigation[] = array(
            'name' => 'Odcinki',
            'link' => '/episodes',
            'icon' => 'fa fa-youtube-play',
        );
        $navigation[] = array(
            'name' => 'Treść',
            'link' => '/news',
            'icon' => 'fa fa-list-alt',
        );
        $navigation[] = array(
            'name' => 'Użytkownicy',
            'link' => '/users',
            'icon' => 'fa fa-users',
        );
        $navigation[] = array(
            'name' => 'Backup',
            'link' => '/backup',
            'icon' => 'fa fa-database',
        );

        $container->get('templating')->addGlobal('navigation', $navigation);

        /** @var \Sequence\Config $config */
        $config = $container->get('config');

        $cache = new Cache();
        $cache->setDriver(new File(array(
            'path' => $config->framework->root_dir.'/apps/Anime/cache/'.$config->framework->environment.'/'
        )));

        $container->set('front_cache', $cache);
    }
} 