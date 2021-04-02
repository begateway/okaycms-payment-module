#!/bin/bash

set -e
set -x

OKAYCMS_INSTALL_FILES="/var/www/html/install"
OKAYCMS_MODULES_DIR="/var/www/html/Okay/Modules/OkayCMS"
BEGATEWAY_DIR="/var/www/app/BeGateway"
SYMLINK_TO_BEGATEWAY=$OKAYCMS_MODULES_DIR"/BeGateway"

if [ -d "$OKAYCMS_MODULES_DIR" ]; then
  if [ -d  "$OKAYCMS_INSTALL_FILES" ]; then
    rm -rf $OKAYCMS_INSTALL_FILES
  fi
  if [ ! -L "$SYMLINK_TO_BEGATEWAY" ]; then
    if [ -d "$BEGATEWAY_DIR" ]; then
      ln -s $BEGATEWAY_DIR $SYMLINK_TO_BEGATEWAY
      chown -h www-data:www-data $SYMLINK_TO_BEGATEWAY
    else
      echo "Error: BeGateway module not found in: "$BEGATEWAY_DIR"!"
    fi
  fi
fi

/usr/local/bin/apache2-foreground

exec "$@"
