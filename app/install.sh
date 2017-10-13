#!/usr/bin/env bash
set -o nounset
set -o errexit
set -o pipefail

declare no_symlinks='on'

# Linux/Mac abstraction
function get_realpath() {
    [[ ! -f "$1" ]] && return 1 # failure : file does not exist.
    [[ -n "$no_symlinks" ]] && local pwdp='pwd -P' || local pwdp='pwd' # do symlinks.
    echo "$( cd "$( echo "${1%/*}" )" 2>/dev/null; $pwdp )"/"${1##*/}" # echo result.
    return 0 # success
}

# Set magic variables for current FILE & DIR
declare -r __FILE__=$(get_realpath ${BASH_SOURCE[0]})
declare -r __DIR__=$(dirname ${__FILE__})

# Source .env file
if [ -f $__DIR__/../.env ]; then
    echo Found .env
    source $__DIR__/../.env
fi


${__DIR__}/../bin/console sw:cache:clear

${__DIR__}/../bin/console sw:database:setup --steps=drop,create,import

if [ $IMPORT_DEMODATA = true ] ; then
    ${__DIR__}/../bin/console sw:database:setup --steps=importDemodata
fi

${__DIR__}/../bin/console sw:database:setup --steps=setupShop --shop-url="$SHOP_URL"

${__DIR__}/create_symlinks.sh

${__DIR__}/../bin/console sw:snippets:to:db --include-plugins

${__DIR__}/../bin/console sw:theme:initialize

${__DIR__}/../bin/console sw:firstrunwizard:disable

${__DIR__}/../bin/console sw:admin:create --name="$ADMIN_NAME" --email="$ADMIN_EMAIL" --username="$ADMIN_USERNAME" --password="$ADMIN_PASSWORD" -n
