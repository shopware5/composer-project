#!/usr/bin/env bash

source $(dirname "$0")/functions.sh
banner

if envFileDoesNotExists
   then
      echo -e "\nHi there! We need to configure your shop before proceeding any further, please complete the following fields\n"
      createEnvFileInteractive
fi

loadEnvFile

noInteraction $@

if [ $DROP_DATABASE = n ]; then
    echo -e "\nNot touching the database, have fun!\n"
    exit 0;
fi

swCommand sw:database:setup --steps=drop,create,import

if [ $IMPORT_DEMODATA = y ]; then
   echo "Importing demo data please wait..."
   swCommand sw:database:setup --steps=importDemodata
fi

createSymLinks

swCommand sw:database:setup --steps=setupShop --shop-url="$SHOP_URL"
swCommand sw:snippets:to:db --include-plugins
swCommand sw:theme:initialize
swCommand sw:firstrunwizard:disable
swCommand sw:plugin:deactivate SwagUpdate
swCommand sw:admin:create --name="$ADMIN_NAME" --email="$ADMIN_EMAIL" --username="$ADMIN_USERNAME" --password="$ADMIN_PASSWORD" -n

if [ $IMPORT_DEMODATA = y ]; then
    if [ $INSTALL_IMAGES = y ]; then
        curl -L "http://releases.s3.shopware.com/test_images_since_5.1.zip" > images.zip && unzip -n images.zip && rm images.zip
    fi
fi

echo -e "\nInstallation finished, have fun!\n"
