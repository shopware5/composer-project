#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

banner
if envFileDoesNotExists
    then
        echo -e "\nPlease run app/bin/install.sh manually to finish your installation\n"
    else
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

        exit 0
    fi


