<?php

$db = array_merge(['port' => 3306], parse_url(getenv(getenv('DATABASE_URL_NAME') ?: 'DATABASE_URL')));

return array_replace_recursive($this->loadConfig($this->AppPath() . 'Configs/Default.php'), [

    'db' => [
        'username' => $db['user'],
        'password' => $db['pass'],
        'dbname'   => substr($db['path'], 1),
        'host'     => $db['host'],
    ],

    'phpSettings' => [
        'display_errors' => 1
    ],

    'front' => array(
        'showException' => true,
        'throwExceptions' => false,
    ),

    'plugin_directories' => [
        'Default'   => $this->AppPath('Plugins_' . 'Default'),
        'Local'     => PROJECTDIR . '/Plugins/Local/',
        'Community' => PROJECTDIR . '/Plugins/Community/',
    ],
]);

