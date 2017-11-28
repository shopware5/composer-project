#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

if envFileDoesNotExists
    then
        banner
        echo -e "\nPlease run app/install.sh manually to finish your installation\n"
    else
        ${__DIR__}/post-update.sh
        exit 0
    fi

echo -e "Have a nice day!\n"
