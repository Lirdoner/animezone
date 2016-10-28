<?php

$config = require $this->appDir.'/Resources/config/settings.php';

$config = array_replace_recursive($config, array(
    'providers' => array(
        'database' => array(
            'default_connection' => 'az_local',
        ),
    ),
    'anime' => array(
        'assets' => '/animezone/public_html/resources/',
        'avatars_dir' => $this->rootDir.'/../public_html/resources/avatars/',
        'category_images' => $this->rootDir.'/../public_html/resources/kategorie/',
    ),
));

return $config;