<?php

use Shopware\Components\DependencyInjection\Container;

class Application extends Shopware
{
    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->docPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}
