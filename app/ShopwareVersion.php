<?php

class ShopwareVersion
{
    public static function parseVersion(string $version): array
    {
        if (!preg_match('/^v?(?<version>[\d]+\.[\d]+\.[\d]+)(\-(?<version_text>[a-z\d]{0,4}))?(@(?<revision>[a-z\d]+)?)?$/i', $version, $versionMatches)) {
            throw new OutOfBoundsException(sprintf('Version "%s" not in expected format', $version));
        }

        return [
            'version' => $versionMatches['version'],
            'version_text' => $versionMatches['version_text'] ?? '',
            'revision' => isset($versionMatches['revision']) ? substr($versionMatches['revision'], 0, 10) : ''
        ];
    }
}
