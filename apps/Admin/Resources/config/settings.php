<?php

$config = require $this->rootDir.'/apps/Anime/Resources/config/settings.php';

$config = array_replace_recursive($config, array(
    'namespaces' => array(
        'Admin\\Provider' => $this->rootDir.'/apps',
        'Dubture\\Monolog' => $this->rootDir.'/vendor/monolog-parser/src',
    ),
    'providers' => array(
        '_globals' => array(
            'class' => 'Admin\\Provider\\GlobalsProvider',
        ),
        'user' => array(
            'options' => array(
                'login_url' => 'login'
            )
        ),
    ),
));

return $config;