#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

banner

loadEnvFile

echo "Updating Shopware install, please wait..."

swCommand sw:migrations:migrate --mode=update
swCommand sw:cache:clear
swCommand sw:theme:cache:generate
createSymLinks

echo -e "\n\nDone! Please login into the backend and open the plugin manager to check for local updates of your plugins to run their respective upgrades.\n"

