#!/bin/bash

set -e;
set -x;

source ./env.sh;

function die () {
  echo 'Error! '$1 1>&2;
  echo 'Installation skipped!' 1>&2;
  exit 1;
}

if [ ! -f docker-compose.yml ]; then
  die "Can't find docker-compose.yml here: "$(pwd);
fi
docker-compose -p $PROJECT_NAME down;
docker-compose -p $PROJECT_NAME build;
docker-compose -p $PROJECT_NAME up -d;
docker-compose -p $PROJECT_NAME ps;



