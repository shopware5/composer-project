#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

if envFileDoesNotExists
    then
        echo -e "\nPlease run ${__DIR__}/${__FILE__} manually to finish your installation"
    else
        ${__DIR__}/post-update.sh
        exit 0
    fi

banner
echo -e "Have a nice day!\n"
