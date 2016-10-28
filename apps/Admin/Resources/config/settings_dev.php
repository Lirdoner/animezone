<?php

$config = require $this->rootDir.'/apps/Admin/Resources/config/settings.php';

$config = array_replace_recursive($config, array(
    'framework' => array(
        //'class_loader' => 'apc',
    ),
    'providers' => array(
        'cache' => array(
            //'driver' => 'Sequence\\Cache\\Driver\\Apc',
        ),
        'database' => array(
            'default_connection' => 'az_local',
        ),
    ),
));

return $config;