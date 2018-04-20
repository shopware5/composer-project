<?php

$db = false;
if (getenv('DATABASE_URL')) {
    $db = parse_url(getenv('DATABASE_URL'));
}

// Fallback if e.g. the password contains URL invalid parameters
if (!$db) {
    $db['user'] = getenv('DB_USERNAME');
    $db['pass'] = getenv('DB_PASSWORD');
    $db['path'] = '/' . getenv('DB_DATABASE');
    $db['host'] = getenv('DB_HOST');
    $db['port'] = getenv('DB_PORT');
    $db['scheme'] = 'mysql';
}

$projectDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;

return array_replace_recursive($this->loadConfig($this->AppPath() . 'Configs/Default.php'), [

    'db' => [
        'username' => $db['user'],
        'password' => $db['pass'],
        'dbname'   => substr($db['path'], 1),
        'host'     => $db['host'],
    ],

    'template' => [
        'templateDir' => $projectDir . 'themes',
    ],

    'plugin_directories' => [
        /*
         * Only use "composer require some/path" to install plugins into the standard Shopware plugin directories.
         * These directories are all .gitignored to prevent the installed plugins from being added to the VCS.
         */
        'Default'   => $this->AppPath('Plugins_' . 'Default'),
        'Local'     => $projectDir . 'Plugins/Local/',
        'Community' => $projectDir . 'Plugins/Community/',
        'ShopwarePlugins' => $projectDir .'custom/plugins/',

        /**
         * Put custom, project specific plugins or plugins bought from the Shopware store FOR THIS SHOP to this directory.
         * They will be added to GIT so you can deploy them with your project.
         */
        'ProjectPlugins' => $projectDir . 'custom/project/',
    ],

    'cdn' => [
        'liveMigration' => true,
        'adapters' => [
            'local' => [
                'path' => $projectDir,
            ],
        ],
    ],

    'app' => [
        'rootDir' => $projectDir,
        'downloadsDir' => $projectDir . 'files/downloads',
        'documentsDir' => $projectDir . 'files/documents',
    ],

    'web' => [
        'webDir' => $projectDir . 'web',
        'cacheDir' => $projectDir . 'web/cache',
    ],
]);
