# Composer template for Shopware projects

Starting with v5.4 Shopware supports installation using composer out of the box. Earlier versions of Shopware are not
supported.

## Installation

```bash
composer create-project shopware/composer-project my_project_name --no-interaction --stability=dev
```

This will clone the repository with all necessary dependencies into a new directory `my_project_name`.

You can then either provide a `.env` file for defining database credentials, the shop-url and Shopware version or have
one created for you using an interactive installer. 

To use the installer, simply run: 

```bash
$ ./app/install.sh
```

## Configuration

Configuration settings like environment specific database settings, API tokens, server IPs or any type of credentials
should be set via environment variables. That way you don't have to include any environment specific or sensitive 
information in your project. You can use a `.env` file for local development or as a workaround.

You can also configure some generic project services in `app/services.xml`. For instance, the error log is configured to
use `stderr` output instead of default log file located at `var/log` directory. New and additional services should be
provided using plugins, though.

## Updating Shopware

Update the version number of `shopware/shopware` in `composer.json`. Then run `composer update shopware/shopware`
to install the new version. Do not forget to commit the new `composer.lock` file to your project afterwards.

### Prepare plugins for composer installation

Given you have a plugin called `SwagMediaSftp` add your dependencies in your plugins `composer.json` file.
Also set the `type` to `shopware-plugin`. See the [SwagMediaSftp](https://github.com/shopwareLabs/SwagMediaSftp)
repository for the complete plugin.

```json
{
    "name": "shopwarelabs/swag-media-sftp",
    "type": "shopware-plugin",
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

For a complete list of the available Shopware-related types see the [Composer Installers](https://github.com/composer/installers) repository.

## Plugins

Plugins being installed like described above are installed into `custom/plugins/`, legacy Shopware plugins are getting 
installed into `Plugins/`. Those directories are included in the `.gitignore` of this repo so plugins installed via composer
are not being tracked by git.

In case you need some project specific plugins that you don't want to add to your project via composer but track them using
this repository, then you can add them to the `custom/project` directory. Only plugins using the new plugin style are
supported.
