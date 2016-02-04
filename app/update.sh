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


$__DIR__/../bin/console sw:migrations:migrate --mode=update || true

$__DIR__/create_symlinks.sh

