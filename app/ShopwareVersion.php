<?php

use Composer\InstalledVersions;

class ShopwareVersion
{
    public static function parseVersion(): array
    {
        $version = InstalledVersions::getVersion('shopware/shopware');
        if (!preg_match('/^v?(?<version>[\d]+\.[\d]+\.[\d]+)\.0(\-(?<version_text>[a-z\d]{0,4}))?$/i', $version, $versionMatches)) {
            throw new OutOfBoundsException(sprintf('Version "%s" not in expected format', $version));
        }

        return [
            'version' => $versionMatches['version'],
            'version_text' => $versionMatches['version_text'] ?? '',
            'revision' => InstalledVersions::getReference('shopware/shopware') ?? ''
        ];
    }
}
