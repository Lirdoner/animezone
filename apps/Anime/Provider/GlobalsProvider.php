<?php


namespace Anime\Provider;


use Anime\EventListener\UserListener;
use Anime\Helper\TextHelper;
use Anime\Model\Ads\AdsManager;
use Anime\Model\Menu\MenuManager;
use Anime\Model\Watch\WatchManager;
use Sequence\Container;
use Sequence\ProviderInterface;
use Symfony\Component\Templating\Helper\AssetsHelper;

class GlobalsProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        date_default_timezone_set('Europe/Warsaw');

        $container->get('dispatcher')->addSubscriber(new UserListener(new WatchManager($container->get('database'))));

        $container->set('video_salt', function(Container $container){
            $videoSalt = $container->get('request')->getSession()->getId();
            $videoSalt .= $container->get('request')->server->get('REMOTE_ADDR');
            $videoSalt .= date('g').'SuPeRs3Cr3t';

            return $videoSalt;
        });

        $container->set('video_prefix', function(){
            $videoPrefix = range('a', 'z');
            $videoPrefix = $videoPrefix[rand(0, 23)].$videoPrefix[rand(0, 23)].$videoPrefix[rand(0, 23)];

            return $videoPrefix;
        });

        $container->get('templating')->set(new TextHelper());
        $container->get('templating')->set(new AssetsHelper($container->get('config')->anime->assets, array()));

        //global variables
        $menu = new MenuManager($container->get('database'), $container->get('cache'));
        $ads = new AdsManager($container->get('database'), $container->get('cache'));

        $container->get('templating')->addGlobal('navigation', $menu->getAll());
        $container->get('templating')->addGlobal('ads', $ads);

        $container->set('ads_manager', $ads);
    }
} 