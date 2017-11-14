#!/usr/bin/env bash

declare -r dir=$(dirname $0)
declare -r file="install.sh"

cat $dir/banner.txt

echo -e "\nPlease run $dir/$file manually to finish your installation"

echo -e "Have a nice day!\n"
