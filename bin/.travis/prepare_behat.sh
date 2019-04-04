#!/bin/bash

PACKAGE_BUILD_DIR=$PWD
EZPLATFORM_BUILD_DIR=${HOME}/build/ezplatform

echo "> Cloning ezsystems/ezplatform"
git clone --depth 1 --single-branch https://github.com/ezsystems/ezplatform.git ${EZPLATFORM_BUILD_DIR}
cd ${EZPLATFORM_BUILD_DIR}

/bin/bash ./bin/.travis/trusty/setup_ezplatform.sh "${COMPOSE_FILE}" '' "${PACKAGE_BUILD_DIR}"
