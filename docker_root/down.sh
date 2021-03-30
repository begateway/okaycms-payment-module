#!/bin/bash

set -e;
set -x;

source ./env.sh;

function die () {
  echo 'Error! '$1 1>&2;
  echo 'Action aborted!' 1>&2;
  exit 1;
}

if [ -f docker-compose.yml ]; then
  docker-compose -p $PROJECT_NAME down;
  docker-compose -p $PROJECT_NAME ps;
else
  die "Can't find docker-compose.yml here: "$(pwd);
fi
