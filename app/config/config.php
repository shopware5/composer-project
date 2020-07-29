<?php

if (isset($_SERVER['DATABASE_URL']) && $db = parse_url($_SERVER['DATABASE_URL'])) {
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
    'trustedproxies' => explode(',', $_SERVER['TRUSTEDPROXIES'] ?? ''),
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
if (($sessionHandler = $_SERVER['SESSION_HANDLER'] ?? null) && ($sessionPath = $_SERVER['SESSION_PATH'] ?? null)) {
    $composerConfig['session'] = [
        'save_handler' => $sessionHandler,
        'save_path'    => $sessionPath,
    ];
}

// Backend Session
// BACKEND_SESSION_HANDLER - redis
// BACKEND_SESSION_PATH - tcp://127.0.0.1:6379
if (($backendSessionHandler = $_SERVER['BACKEND_SESSION_HANDLER'] ?? null) && ($backendSessionPath = $_SERVER['BACKEND_SESSION_PATH'] ?? null)) {
    $composerConfig['backendsession'] = [
        'save_handler' => $sessionHandler,
        'save_path'    => $sessionPath,
    ];
}

// Model Cache
// MODEL_CACHE_HANDLER - redis
// MODEL_CACHE_HOST - tcp://127.0.0.1:6379/1
if (($modelCacheHandler = $_SERVER['MODEL_CACHE_HANDLER'] ?? null) && ($modelCacheHost = parse_url($_SERVER['MODEL_CACHE_HOST'] ?? null))) {
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
if ($zendCacheHandler = $_SERVER['ZEND_CACHE_HANDLER'] ?? null) {
    $zendCacheHosts = [];
    while (($zendCacheHost = $_SERVER['ZEND_CACHE_HOST_' . count($zendCacheHosts)] ?? null) && $zendCacheHost = parse_url($zendCacheHost)) {
        $zendCacheHosts[] = [
            'host'    => $zendCacheHost['host'],
            'port'    => $zendCacheHost['port'],
            'dbindex' => (int)substr($zendCacheHost['path'], 1),
        ];
    }
    $composerConfig['cache'] = [
        'backend'        => $zendCacheHandler, // e.G auto, apcu, xcache
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
if ($esEnabled = $_SERVER['ES_ENABLED'] ?? null) {
    $composerConfig['es'] = [
        'enabled'                 => ((bool)$esEnabled),
        'prefix'                  => ($esPrefix = $_SERVER['ES_PREFIX'] ?? false) === false ? 'sw_dev' : $esPrefix,
        'number_of_replicas'      => ($esPrefix = $_SERVER['ES_REPLICAS'] ?? false) === false ? null : $esPrefix,
        'number_of_shards'        => ($esShards = $_SERVER['ES_SHARDS'] ?? false) === false ? null : $esShards,
        'version'                 => ($esVersion = $_SERVER['ES_VERSION'] ?? false) === false ? '' : $esVersion,
        'dynamic_mapping_enabled' => ($esDynamicMapping = $_SERVER['ES_DYNAMIC_MAPPING_ENABLED'] ?? false) === false ? null : $esDynamicMapping,
        'client'                  => [
            'hosts' => ($esHosts = $_SERVER['ES_HOSTS'] ?? false) === false ? [] : explode(',', $esHosts)
        ],
        'backend'                 => [
            'write_backlog' => (bool)$_SERVER['ES_BACKEND_ENABLED'] ?? false,
            'enabled'       => (bool)$_SERVER['ES_BACKEND_BACKLOG'] ?? false,
        ],
    ];
}

// Custom Config
// CUSTOM_CONFIG_FILE - config.php
if (($customConfigFile = $_SERVER['CUSTOM_CONFIG_FILE'] ?? null) && ($customConfigFile = ($projectDir . DIRECTORY_SEPARATOR . $customConfigFile)) && file_exists($customConfigFile)) {
    $customConfig = include $customConfigFile;

    $composerConfig = array_merge_recursive($composerConfig, $customConfig[0] ?? []);
    $composerConfig = array_replace_recursive($composerConfig, $customConfig[1] ?? []);
}

return array_replace_recursive($this->loadConfig($this->AppPath() . 'Configs/Default.php'), $composerConfig);