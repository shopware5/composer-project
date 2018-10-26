<?php

$db = false;

if (getenv('DATABASE_URL') && $db = parse_url(getenv('DATABASE_URL'))) {
    $db = array_map('rawurldecode', $db);
    $db['path'] = substr($db['path'], 1);
} else {
    // Fallback if e.g. the password contains URL invalid parameters
    $db['user'] = getenv('DB_USERNAME');
    $db['pass'] = getenv('DB_PASSWORD');
    $db['path'] = getenv('DB_DATABASE');
    $db['host'] = getenv('DB_HOST');
    $db['port'] = getenv('DB_PORT');
    $db['scheme'] = 'mysql';
}

$projectDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;

return array_replace_recursive($this->loadConfig($this->AppPath() . 'Configs/Default.php'), [

    'db' => [
        'username' => $db['user'],
        'password' => $db['pass'],
        'dbname'   => $db['path'],
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
        /**
         * Since Shopware 5.5 the configuration for 'downloadsDir' and 'documentsDir'
         * have become obsolete and are now handled by the defined filesystem adapters below.
         */
        'downloadsDir' => $projectDir . 'files/downloads',
        'documentsDir' => $projectDir . 'files/documents',
    ],

    /**
     * The PrefixFilesystem is available since Shopware 5.5 and allows plugins
     * to use dedicated filesystems for public and private files.
     *
     * @see https://developers.shopware.com/developers-guide/shopware-5-upgrade-guide-for-developers/#filesystem-abstraction-layer
     */
    'filesystem' => [
        'private' => [
            'config' => [
                'root' => $projectDir . 'files' . DIRECTORY_SEPARATOR,
            ],
        ],
        'public' => [
            'config' => [
                'root' => $projectDir . 'web' . DIRECTORY_SEPARATOR,
            ],
        ],
    ],

    'web' => [
        'webDir' => $projectDir . 'web',
        'cacheDir' => $projectDir . 'web/cache',
    ],
]);
