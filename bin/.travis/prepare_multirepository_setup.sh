#!/bin/bash

cd "$HOME/build/ezplatform";

# Drop database used by default connection
docker-compose exec --user www-data app sh -c "php bin/console doctrine:database:drop --connection=default --force"

# Run setup
docker-compose exec --user www-data app sh -c "bin/ezbehat --mode=standard --profile=setup --suite=multirepository"
docker-compose exec --user www-data app sh -c "composer run post-install-cmd"

# Reinstal database using the new repository
docker-compose exec --user www-data app sh -c "composer ezplatform-install"