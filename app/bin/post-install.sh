#!/usr/bin/env bash

source $(dirname "$0")/functions.sh

createSymLinks

set -eu

script_dir="$(cd -- "$(dirname "$0")" && pwd)"
root_dir="$script_dir"/../..

set -x

rm -rf -- "$root_dir"/vendor/mpdf/mpdf/ttfonts
rm -rf -- "$root_dir"/vendor/google/protobuf/src
rm -rf -- "$root_dir"/vendor/google/protobuf/java
rm -rf -- "$root_dir"/vendor/google/protobuf/objectivecs
rm -rf -- "$root_dir"/vendor/google/protobuf/csharp
rm -rf -- "$root_dir"/vendor/google/protobuf/python
rm -rf -- "$root_dir"/vendor/google/protobuf/ruby
rm -rf -- "$root_dir"/vendor/google/protobuf/js
rm -rf -- "$root_dir"/vendor/google/protobuf/javanano
rm -rf -- "$root_dir"/vendor/google/protobuf/php/ext
rm -rf -- "$root_dir"/vendor/google/protobuf/php/tests
rm -rf -- "$root_dir"/vendor/google/cloud/tests
rm -rf -- "$root_dir"/vendor/google/cloud/dev
rm -rf -- "$root_dir"/vendor/google/cloud/docs

set +eux

if envFileDoesNotExists
    then
        banner
        echo -e "\nPlease run app/bin/install.sh manually to finish your installation\n"
    else
        ${__DIR__}/post-update.sh
        exit 0
    fi

echo -e "Have a nice day!\n"
