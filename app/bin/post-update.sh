#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

banner

loadEnvFile

echo "Updating Shopware install, please wait..."

createSymLinks

swCommand sw:migrations:migrate --mode=update
swCommand sw:theme:synchronize
swCommand sw:cache:clear
swCommand sw:theme:cache:generate
swCommand sw:plugin:refresh
swCommand sw:plugin:update --batch=active
swCommand sw:snippets:to:db

echo -e "\n\nDone!\n"

