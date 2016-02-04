#!/usr/bin/env bash
set -o nounset
set -o errexit
set -o pipefail

# Set magic variables for current FILE & DIR
declare -r __FILE__=$(readlink -f ${BASH_SOURCE[0]})
declare -r __DIR__=$(dirname $__FILE__)

cd $__DIR__/..
echo "Create symlinks in $__DIR__"

rm -rf  web/themes/Backend/ExtJs/backend
mkdir -p web/themes/Backend/ExtJs/backend
ln -s ../../../../../vendor/shopware/shopware/themes/Backend/ExtJs/backend/_resources web/themes/Backend/ExtJs/backend/_resources

rm -rf  web/engine/Library
mkdir -p web/engine/Library
ln -s ../../../vendor/shopware/shopware/engine/Library/CodeMirror web/engine/Library/CodeMirror
ln -s ../../../vendor/shopware/shopware/engine/Library/ExtJs  web/engine/Library/ExtJs
ln -s ../../../vendor/shopware/shopware/engine/Library/TinyMce web/engine/Library/TinyMce

rm -rf web/themes/Frontend/Responsive/frontend/
mkdir -p web/themes/Frontend/Responsive/frontend/
ln -s ../../../../../vendor/shopware/shopware/themes/Frontend/Responsive/frontend/_public web/themes/Frontend/Responsive/frontend/_public
