<?php

return array(
    'framework' => array(
        //'class_loader' => 'xcache',
    ),
    'namespaces' => array(
        'Anime\\Model' => $this->rootDir.'/apps',
        'Anime\\Provider' => $this->rootDir.'/apps',
        'Anime\\Helper' => $this->rootDir.'/apps',
        'Anime\\EventListener' => $this->rootDir.'/apps',
        'Anime\\Command' => $this->rootDir.'/apps',
        'Gregwar\\Captcha' => $this->rootDir.'/vendor',
    ),
    'commands' => array(
        'Sequence\\Command\\CacheClearCommand',
        'Anime\\Command\\ClearOldCommand',
        'Anime\\Command\\DatabaseBackupCommand',
        'Anime\\Command\\StatisticsCommand',
    ),
    'providers' => array(
        'cache' => array(
            //'driver' => 'Sequence\\Cache\\Driver\\Xcache',
        ),
        'session' => array(
            'enabled' => true,
            'options' => array(
                'cookie_lifetime' => 2592000,
            ),
        ),
        'database' => array(
            'default_connection' => 'animezone_md',
            'connections' => array(
                'animezone_md' => array(
                    'username' => 'm1018_animezone',
                    'password' => 'DY95KHbmOZdsVzEdmlqh',
                    'dsn' => 'mysql:host=mysql3.mydevil.net;dbname=m1018_animezone',
                    'driver_options' => array(\PDO::ATTR_PERSISTENT => true),
                ),
            ),
            'enabled' => true,
        ),
        'user' => array(
            'options' => array(
                'verification' => 1,
                'groups' => false,
                'firewall' => true,
                'login_url' => 'login_mobile'
            ),
            'enabled' => true,
        ),
        '_globals' => array(
            'class' => 'Anime\\Provider\\GlobalsProvider',
        ),
        'mailer' => array(
            'class' => 'Sequence\\Provider\\MailerProvider',
            'options' => array(
                'send_from' => 'no-reply@animezone.pl',
                'send_name' => 'AnimeZone.pl',
            ),
        ),
    ),
    'anime' => array(
        'title' => 'AnimeZone.pl - Twoja strefa anime online pl!',
        'description' => 'AnimeZone.pl - Twoja strefa anime online pl! Tu znajdziesz najnowsze odcinki anime online pl oraz anime HD do pobrania: Naruto, Bleach, Dragon Ball, One Piece, Fairy Tail czy też D.Gray-Man.',
        'keywords' => 'anime, anime online pl, anime HD do pobrania, animezone, naruto, dragon ball kai, bleach, d.gray-man, one piece, soul eater, fullmetal alchemist, death note, busou renkin, fairy tail',
        'error' => array(
            403 => 'Niestety, strona o podanym adresie jest chroniona.',
            404 => 'Niestety, strona o podanym adresie nie istnieje.',
            500 => 'Niestety serwer, na którym znajduje się strona jest przeciążony.'
        ),
        'facebook' => array(
            'appId' => '154975637861911',
            'secret' => '430a1b0611c71e11ac5cc46e6cd8a15d',
        ),
        'assets' => '/resources/',
        'avatars_dir' => $this->rootDir.'/../public_html/resources/avatars/',
        'category_images' => $this->rootDir.'/../public_html/resources/kategorie/',
    ),
);