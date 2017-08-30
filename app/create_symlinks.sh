#!/usr/bin/env bash
set -o nounset
set -o errexit
set -o pipefail

# Set magic variables for current FILE & DIR
declare -r __FILE__=$(readlink -f ${BASH_SOURCE[0]})
declare -r __DIR__=$(dirname $__FILE__)

cd $__DIR__/..
echo "Create symlinks in $__DIR__"

rm -rf  engine/Library
mkdir -p engine/Library
ln -s ../../vendor/shopware/shopware/engine/Library/CodeMirror engine/Library/CodeMirror
ln -s ../../vendor/shopware/shopware/engine/Library/ExtJs  engine/Library/ExtJs
ln -s ../../vendor/shopware/shopware/engine/Library/TinyMce engine/Library/TinyMce

rm -rf themes/Frontend/Bare
rm -rf themes/Frontend/Responsive
rm -rf themes/Backend/ExtJs
mkdir -p themes/Frontend
mkdir -p themes/Backend
ln -s ../../vendor/shopware/shopware/themes/Backend/ExtJs themes/Backend/ExtJs
ln -s ../../vendor/shopware/shopware/themes/Frontend/Bare themes/Frontend/Bare
ln -s ../../vendor/shopware/shopware/themes/Frontend/Responsive themes/Frontend/Responsive

# Medien
#if [ ! -d media ]; then
#    cp -r vendor/shopware/shopware/media ./
#fi
#rm -rf vendor/shopware/shopware/media
#ln -s ../../../media vendor/shopware/shopware/media

# Files
#if [ ! -d files ]; then
#    cp -r vendor/shopware/shopware/files ./
#fi
#rm -rf vendor/shopware/shopware/files
#ln -s ../../../files vendor/shopware/shopware/files

# Web
#if [ ! -d web ]; then
#    cp -r vendor/shopware/shopware/web ./
#fi
#rm -rf vendor/shopware/shopware/web
#ln -s ../../../web vendor/shopware/shopware/web

#rm -rf Plugins/Default
#ln -s ../vendor/shopware/shopware/engine/Shopware/Plugins/Default Plugins/Default