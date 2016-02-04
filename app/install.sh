#!/usr/bin/env bash
set -o nounset
set -o errexit
set -o pipefail

# Set magic variables for current FILE & DIR
declare -r __FILE__=$(readlink -f ${BASH_SOURCE[0]})
declare -r __DIR__=$(dirname $__FILE__)

# Source .env file
if [ -f $__DIR__/../.env ]; then
    echo Found .env
    source $__DIR__/../.env
fi

$__DIR__/../bin/console sw:cache:clear

$__DIR__/../bin/console sw:database:setup --steps=drop,create,import

if [ $IMPORT_DEMODATA = true ] ; then
    $__DIR__/../bin/console sw:database:setup --steps=importDemodata
fi

$__DIR__/../bin/console sw:database:setup --steps=setupShop --shop-url="$SHOP_URL"

$__DIR__/../bin/console sw:snippets:to:db --include-plugins

$__DIR__/../bin/console sw:theme:initialize

$__DIR__/../bin/console sw:firstrunwizard:disable

$__DIR__/../bin/console sw:admin:create --name="$ADMIN_NAME" --email="$ADMIN_EMAIL" --username="$ADMIN_USERNAME" --password="$ADMIN_PASSWORD" -n

$__DIR__/create_symlinks.sh

