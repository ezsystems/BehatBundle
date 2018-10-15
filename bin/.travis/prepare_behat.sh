#!/bin/sh

# File for setting up system for behat testing, just like it's done in Kernel's .travis.yml

# Change local git repo to be a full one as we will reuse it for composer install below
git fetch --unshallow && git checkout -b tmp_ci_branch
export BRANCH_BUILD_DIR=$TRAVIS_BUILD_DIR TRAVIS_BUILD_DIR="$HOME/build/ezplatform"

cd "$HOME/build"

git clone --depth 1 --single-branch --branch $EZPLATFORM_BRANCH https://github.com/ezsystems/ezplatform.git
cd ezplatform

# Install everything needed for behat testing, using our local branch of this repo
./bin/.travis/trusty/setup_from_external_repo.sh $BRANCH_BUILD_DIR "ezsystems/BehatBundle:dev-tmp_ci_branch"
