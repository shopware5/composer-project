#!/usr/bin/env bash

source $(dirname "$0")/functions.sh
banner

if envFileDoesNotExists
   then
      echo -e "\nHi there! We need to configure your shop before proceeding any further, please complete the following fields\n"
      createEnvFileInteractive
fi

loadEnvFile

BC=$'\e[41m\e[97m'
EC=$'\e[0m'
read -p "Start installation? This will drop the database ${BC}$DATABASE_URL${EC}! (y/N) " DROP_DATABASE

DROP_DATABASE=${DROP_DATABASE:-"n"}
[ $DROP_DATABASE = n ] && exit 1;

swCommand sw:database:setup --steps=drop,create,import

if [ $IMPORT_DEMODATA = y ] ; then
   echo "Importing demo data please wait..."
   swCommand sw:database:setup --steps=importDemodata
fi

createSymLinks

swCommand sw:database:setup --steps=setupShop --shop-url="$SHOP_URL"
swCommand sw:snippets:to:db --include-plugins
swCommand sw:theme:initialize
swCommand sw:firstrunwizard:disable
swCommand sw:admin:create --name="$ADMIN_NAME" --email="$ADMIN_EMAIL" --username="$ADMIN_USERNAME" --password="$ADMIN_PASSWORD" -n

echo -e "Installation finished, have fun!\n"
[ $IMPORT_DEMODATA = y ] && echo -e "To install the demodata images (~285MB), run:\ncurl -L http://releases.s3.shopware.com/test_images_since_5.1.zip > images.zip && unzip images.zip && rm images.zip\n";
