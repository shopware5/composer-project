#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

banner

loadEnvFile

echo "Updating Shopware install, please wait..."

createSymLinks

swCommand sw:migrations:migrate --mode=update
swCommand sw:cache:clear
swCommand sw:theme:cache:generate
swCommand sw:plugin:update --batch=active

echo -e "\n\nDone!\n"

