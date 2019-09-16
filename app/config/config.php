<?php

if (getenv('DATABASE_URL') && $db = parse_url(getenv('DATABASE_URL'))) {
    $db = array_map('rawurldecode', $db);
    $db['path'] = substr($db['path'], 1);
    if (!isset($db['pass'])) {
        $db['pass'] = '';
    }
} else {
    die('Critical environment variable \'DATABASE_URL\' missing!' . PHP_EOL);
}

$projectDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;

$composerConfig = [
    'trustedproxies' => explode(',', getenv('TRUSTEDPROXIES')),
    'db'             => [
        'username' => $db['user'],
        'password' => $db['pass'],
        'dbname'   => $db['path'],
        'host'     => $db['host'],
        'port'     => $db['port'],
    ],

    'template' => [
        'templateDir' => $projectDir . 'themes',
    ],

    'plugin_directories' => [
        /*
         * Only use "composer require some/path" to install plugins into the standard Shopware plugin directories.
         * These directories are all .gitignored to prevent the installed plugins from being added to the VCS.
         */
        'Default'         => $this->AppPath('Plugins_' . 'Default'),
        'Local'           => $projectDir . 'Plugins/Local/',
        'Community'       => $projectDir . 'Plugins/Community/',
        'ShopwarePlugins' => $projectDir . 'custom/plugins/',

        /**
         * Put custom, project specific plugins or plugins bought from the Shopware store FOR THIS SHOP to this directory.
         * They will be added to GIT so you can deploy them with your project.
         */
        'ProjectPlugins'  => $projectDir . 'custom/project/',
    ],

    'cdn' => [
        'liveMigration' => false,
        'adapters'      => [
            'local' => [
                'path' => $projectDir,
            ],
        ],
    ],

    'app'        => [
        'rootDir'      => $projectDir,
        /**
         * These parameters were necessary in Shopware 5.4.x and are replaced by the filesystem below in 5.5.x
         */
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
        'public'  => [
            'config' => [
                'root' => $projectDir . 'web' . DIRECTORY_SEPARATOR,
            ],
        ],
    ],

    'web' => [
        'webDir'   => $projectDir . 'web',
        'cacheDir' => $projectDir . 'web/cache',
    ],
];

// Session
// SESSION_HANDLER - redis
// SESSION_PATH - tcp://127.0.0.1:6379
if (($sessionHandler = getenv('SESSION_HANDLER')) && ($sessionPath = getenv('SESSION_PATH'))) {
    $composerConfig['session'] = [
        'save_handler' => $sessionHandler,
        'save_path'    => $sessionPath,
    ];
}

// Backend Session
// BACKEND_SESSION_HANDLER - redis
// BACKEND_SESSION_PATH - tcp://127.0.0.1:6379
if (($backendSessionHandler = getenv('BACKEND_SESSION_HANDLER')) && ($backendSessionPath = getenv('BACKEND_SESSION_PATH'))) {
    $composerConfig['backendsession'] = [
        'save_handler' => $sessionHandler,
        'save_path'    => $sessionPath,
    ];
}

// Model Cache
// MODEL_CACHE_HANDLER - redis
// MODEL_CACHE_HOST - tcp://127.0.0.1:6379/1
if (($modelCacheHandler = getenv('MODEL_CACHE_HANDLER')) && ($modelCacheHost = parse_url(getenv('MODEL_CACHE_HOST')))) {
    $composerConfig['model'] = [
        'redisHost'     => $modelCacheHost['host'],
        'redisPort'     => $modelCacheHost['port'],
        'redisDbIndex'  => (int)substr($modelCacheHost['path'], 1),
        'cacheProvider' => $modelCacheHandler
    ];
}
// Zend Cache
// ZEND_CACHE_HANDLER - redis
// ZEND_CACHE_HOST_0 - tcp://127.0.0.1:6379/1
if ($httpCacheHandler = getenv('ZEND_CACHE_HANDLER')) {
    $zendCacheHosts = [];
    while (($zendCacheHost = getenv('ZEND_CACHE_HOST_' . count($zendCacheHosts))) && $zendCacheHost = parse_url($zendCacheHost)) {
        $zendCacheHosts[] = [
            'host'    => $zendCacheHost['host'],
            'port'    => $zendCacheHost['port'],
            'dbindex' => (int)substr($zendCacheHost['path'], 1),
        ];
    }
    $composerConfig['cache'] = [
        'backend'        => $httpCacheHandler, // e.G auto, apcu, xcache
        'backendOptions' => [
            'servers' => $zendCacheHosts,
        ],
    ];
}

// ElasticSearch
// ES_ENABLED - false
// ES_PREFIX - "sw_dev"
// ES_REPLICAS - null
// ES_SHARDS - null
// ES_VERSION - 5.6.5
// ES_DYNAMIC_MAPPING_ENABLED - true
// ES_HOSTS - "localhost:9200"
// ES_BACKEND_ENABLED - false
// ES_BACKEND_BACKLOG - false
if ($esEnabled = getenv('ES_ENABLED')) {
    $composerConfig['es'] = [
        'enabled'                 => ((bool)$esEnabled),
        'prefix'                  => ($esPrefix = getenv('ES_PREFIX')) === false ? 'sw_dev' : $esPrefix,
        'number_of_replicas'      => ($esPrefix = getenv('ES_REPLICAS')) === false ? null : $esPrefix,
        'number_of_shards'        => ($esShards = getenv('ES_SHARDS')) === false ? null : $esShards,
        'version'                 => ($esVersion = getenv('ES_VERSION')) === false ? '' : $esVersion,
        'dynamic_mapping_enabled' => ($esDynamicMapping = getenv('ES_DYNAMIC_MAPPING_ENABLED')) === false ? null : $esDynamicMapping,
        'client'                  => [
            'hosts' => ($esHosts = getenv('ES_HOSTS')) === false ? [] : explode(',', $esHosts)
        ],
        'backend'                 => [
            'write_backlog' => (bool)getenv('ES_BACKEND_ENABLED'),
            'enabled'       => (bool)getenv('ES_BACKEND_BACKLOG'),
        ],
    ];
}

// Custom Config
// CUSTOM_CONFIG_FILE - config.php
if (($customConfigFile = getenv('CUSTOM_CONFIG_FILE')) && ($customConfigFile = ($projectDir . DIRECTORY_SEPARATOR . $customConfigFile)) && file_exists($customConfigFile)) {
    $customConfig = include $customConfigFile;

    $composerConfig = array_merge_recursive($composerConfig, $customConfig[0] ?? []);
    $composerConfig = array_replace_recursive($composerConfig, $customConfig[1] ?? []);
}

return array_replace_recursive($this->loadConfig($this->AppPath() . 'Configs/Default.php'), $composerConfig);