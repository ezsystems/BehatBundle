#!/bin/bash

# Run a regression build on Travis using multiple dependencies (Pull Requests)
#
# How to use:
# - run the script in "behat" Symfony environment
# - specify the number of Pull Requests and paste GitHub links to them
#
# Requires GitHub CLI tool ("hub"): https://github.com/github/hub

set -e

if [ "$SYMFONY_ENV" != "behat" ]; then
   echo 'Please run the script in "behat" Symfony env.'
   echo 'You can run "export SYMFONY_ENV=behat" in the command line to achieve it.'
   echo 'Aborted.'
   exit 1
fi

if ! [ -x "$(command -v hub)" ]; then
   echo '"hub" command is not available.'
   echo 'Install the GitHub CLI client by going to https://github.com/github/hub and rerun the script.'
   echo 'Aborted.'
   exit 1
fi

# Step 0: Setup variables
CONST_RANDOM_STRING=$(uuidgen)
prLinks=()
composerDependencyStrings=()
REPOSITORY_URLS=()
GITHUB_OAUTH_TOKEN=$(composer config github-oauth.github.com --global)
TARGET_METAREPOSITORY_BRANCH=""
TARGET_PAGE_BUILDER_BRANCH=""

# Step 1: ask for number of PRs and a link for each PR

read -p 'Number of pull requests: ' numberOfDependencies

for ((n=0;n<$numberOfDependencies;n++)); do
  read -p 'Pull request link:' link
  prLinks+=("$link")
done

echo 'Adding dependencies to Composer...'

# Step 2: Parse data from the Pull Request
for i in "${prLinks[@]}"
do
  read -r -a VALUES <<< "$(php bin/console ezplatform:tools:get-pull-request-data $i $GITHUB_OAUTH_TOKEN)"

  REPOSITORY_URLS+=("${VALUES[0]}")

  BRANCH_NAME="${VALUES[1]}"
  BRANCH_ALIAS="${VALUES[2]}"
  REPOSITORY="${VALUES[3]}"

  composerDependencyString=$(printf "ezsystems/%s:dev-%s as %s" $REPOSITORY $BRANCH_NAME $BRANCH_ALIAS)
  composerDependencyStrings+=("$composerDependencyString")

  TARGET_METAREPOSITORY_BRANCH="${VALUES[4]}"
  TARGET_PAGE_BUILDER_BRANCH="${VALUES[5]}"
done

# Step 3: Prepare ezplatfom-ee repository
rm -rf regression_setup
mkdir -p regression_setup && cd regression_setup
git clone --quiet http://github.com/ezsystems/ezplatform-ee.git -b $TARGET_METAREPOSITORY_BRANCH
cd ezplatform-ee
git remote remove origin 2> /dev/null
METAREPOSITORY_BRANCH_NAME=$CONST_RANDOM_STRING
git checkout -b $METAREPOSITORY_BRANCH_NAME --quiet

# Step 4: Execute Composer commands in ezplatform-ee

for i in "${REPOSITORY_URLS[@]}"
do
  composer config repositories.$(uuidgen) vcs "$i"
done

for i in "${composerDependencyStrings[@]}"
do
  printf "Adding dependency: %s\n" "$i"
  composer require --no-update --no-progress --no-scripts "$i"
done

echo 'Dependencies have been added ✔'

# Step 5: Push the metarepo

echo 'Preparing metarepository branch...'
git add composer.json
git commit -m "[Travis] Added dependencies]" --quiet
git remote add regression-remote http://github.com/mnocon/ezplatform-ee.git
git push --set-upstream regression-remote $METAREPOSITORY_BRANCH_NAME --quiet > /dev/null
cd ..

echo 'Prepared a branch with dependencies using ezplatform-ee repository ✔'

## Step 6: Trigger a build in Page Builder using a Pull Request

git clone --quiet http://github.com/ezsystems/ezplatform-page-builder.git -b $TARGET_PAGE_BUILDER_BRANCH --quiet
cd ezplatform-page-builder
git checkout -b $CONST_RANDOM_STRING --quiet || exit 1
git branch -D $TARGET_PAGE_BUILDER_BRANCH --quiet || exit 1
sed -i '' -e 's/https:\/\/github.com\/ezsystems\/ezplatform-ee.git/https:\/\/github.com\/mnocon\/ezplatform-ee.git/g' .travis.yml
composer config extra._ezplatform_branch_for_behat_tests $METAREPOSITORY_BRANCH_NAME
git add composer.json .travis.yml
git commit -m "Run regression" --quiet

# Step 7: Create a Pull Request and open it
printf "About to push in Page Builder to branch %s\n" "$(git rev-parse --abbrev-ref HEAD)"
read -p "Do you want to continue? " -n 1 -r
echo ''
if [[ $REPLY =~ ^[Yy]$ ]]
then
  git push --set-upstream origin $CONST_RANDOM_STRING --quiet  > /dev/null
  echo 'Prepared a branch in Page Builder repository ✔'

  hub pull-request -b $TARGET_PAGE_BUILDER_BRANCH -m "[DON'T MERGE] Running regression tests" -m "Add your description here." -l "Work in progress" --browse
  echo 'Prepared a Pull Request in Page Builder triggering the build ✔'
  exit 0
fi

printf 'Aborted.\n'
exit 1
