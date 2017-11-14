# Composer template for Shopware projects

## Installation

```bash
composer create-project shopware/shopware-composer-project my_project_name --no-interaction --stability=dev
```

During installation `.env.example` is copied to `.env`.
Please configure database credentials here.

```bash
$ ./app/install.sh
```

## Updating Shopware

Update the version number of shopware/shopware composer.json.
Run composer update shopware/shopware.

## Plugins

Legacy Shopware plugins are getting installed into `Plugins/`, new plugins into `custom/plugins/`.

### Prepare plugins for composer installation

Given you have a plugin called `SwagMediaSftp` (`Shopware_Plugins_Frontend_SwagMediaSftp_Bootstrap`) add your dependencies in your plugins `composer.json` file.
Also set the `type` to `shopware-frontend-plugin`.

```json
{
    "name": "shopwarelabs/swag-media-sftp",
    "type": "shopware-frontend-plugin",
    "description": "The SFTP adapter allows you to manage your media files in shopware on a SFTP environment.",
    "license": "MIT",
    "extra": {
        "installer-name": "SwagMediaSftp"
    },
    "require": {
        "league/flysystem-sftp": "^1.0"
    }
}
```

