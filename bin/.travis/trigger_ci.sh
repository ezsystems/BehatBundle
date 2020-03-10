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
HAS_PAGE_BUILDER_DEPENDENCY=0

# Step 1: ask for number of PRs and a link for each PR

read -p 'Number of pull requests: ' numberOfDependencies

for ((n=0;n<$numberOfDependencies;n++)); do
  read -p 'Pull request link: ' link
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
  REPOSITORY_NAME="${VALUES[3]}"

  if [ "${REPOSITORY_NAME}" == "ezplatform-page-builder" ] ; then
    # We need to detect Page Builder because the regression is run by creating a PR in that repo - if there is a PR already we don't need to create a new one
    HAS_PAGE_BUILDER_DEPENDENCY=1
    PAGE_BUILDER_PR_BRANCH=$BRANCH_NAME
  elif [ "${HAS_PAGE_BUILDER_DEPENDENCY}" == 0 ]; then
    # If Page Builder dependency has been detected for earlier links do not overwrite it
    PAGE_BUILDER_PR_BRANCH=$CONST_RANDOM_STRING
  fi

  composerDependencyString=$(printf "ezsystems/%s:dev-%s as %s" $REPOSITORY_NAME $BRANCH_NAME $BRANCH_ALIAS)
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

echo -e 'Dependencies have been added \033[0;32m✔\033[0m'

# Step 5: Push the metarepo

echo 'Preparing metarepository branch...'
git add composer.json
git commit -m "[Travis] Added dependencies]" --quiet
git remote add regression-remote http://github.com/mnocon/ezplatform-ee.git
git push --set-upstream regression-remote $METAREPOSITORY_BRANCH_NAME --quiet > /dev/null
cd ..

echo -e 'Prepared a branch with dependencies using ezplatform-ee repository \033[0;32m✔\033[0m'

## Step 6: Clone Page Builder repository

if [ "${HAS_PAGE_BUILDER_DEPENDENCY}" == 1 ] ; then
  # If there is a Page Builder dependency then we need to add a commit there, not to a new branch
  git clone --quiet http://github.com/ezsystems/ezplatform-page-builder.git -b $PAGE_BUILDER_PR_BRANCH
  cd ezplatform-page-builder
else
  # Without an existing Page Builder branch we base a new branch on the target branch
  git clone --quiet http://github.com/ezsystems/ezplatform-page-builder.git -b $TARGET_PAGE_BUILDER_BRANCH
  cd ezplatform-page-builder
  git checkout -b $PAGE_BUILDER_PR_BRANCH --quiet || exit 1
  git branch -D $TARGET_PAGE_BUILDER_BRANCH --quiet || exit 1
fi

# Step 7: Prepare a commit in Page Builder

sed -i.bak -e 's/https:\/\/github.com\/ezsystems\/ezplatform-ee.git/https:\/\/github.com\/mnocon\/ezplatform-ee.git/g' .travis.yml
composer config extra._ezplatform_branch_for_behat_tests $METAREPOSITORY_BRANCH_NAME
git add composer.json .travis.yml
git commit -m "[TMP] Run regression" --quiet

# Step 8: Push to Page Builder. Open existing PR or create a new one
printf "About to push in Page Builder to branch \033[1;32m%s\033[0m\n" "$(git rev-parse --abbrev-ref HEAD)"
if [ "${HAS_PAGE_BUILDER_DEPENDENCY}" == 1 ] ; then
  echo -e "\033[1;33mThis is an existing branch that contains work done by someone else.\033[0m"
  echo -e "\033[1;33mRemember to talk with them that you are adding a commit to their branch and clearly mark the Pull Request as one containing temporary changes.\033[0m"
fi
read -p "Do you want to continue? " -n 1 -r
echo ''

if [[ ! $REPLY =~ ^[Yy]$ ]] ; then
  echo -e '\033[0;31mAborted.\033[0m'
  exit 1
fi

# Push to Page Builder
git push --set-upstream origin $PAGE_BUILDER_PR_BRANCH --quiet  > /dev/null
echo -e 'Pushed to Page Builder repository \033[0;32m✔\033[0m'

if [ "${HAS_PAGE_BUILDER_DEPENDENCY}" == 1 ] ; then
  # Open existing PR
  echo -e '\033[1;33mOpening existing Pull Request. Remember to indicate that it contains temporary commit.\033[0m'
  hub pr show
else
  # Without an existing Page Builder branch we need to create a new PR
  hub pull-request -b $TARGET_PAGE_BUILDER_BRANCH -m "[DON'T MERGE] Running regression tests" -m "Add your description here." -l "Work in progress" --browse
  echo -e 'Prepared a Pull Request in Page Builder triggering the build \033[0;32m✔\033[0m'
fi

echo -e 'Script finished \033[0;32m✔\033[0m'
