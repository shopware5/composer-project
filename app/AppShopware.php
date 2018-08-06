<?php

use Shopware\Components\DependencyInjection\Container;

/**
 * This overrides the original Shopware class and fixes path specification.
 * Those paths could be used by other code via \Shopware::DocPath()
 */
class AppShopware extends \Shopware
{

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->docPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

}
