#!/bin/bash

cd "$HOME/build/project";

# Drop database used by default connection
docker-compose --env-file=.env exec --user www-data app sh -c "php bin/console doctrine:database:drop --connection=default --force"

# Run setup
docker-compose --env-file=.env exec --user www-data app sh -c "vendor/bin/ezbehat --mode=standard --profile=setup --suite=multirepository -c=behat_ibexa_oss.yaml"
docker-compose --env-file=.env exec --user www-data app sh -c "composer run post-install-cmd"

# Reinstal database using the new repository
docker-compose --env-file=.env exec --user www-data app sh -c "php bin/console ibexa:install"
