## Object Management

BehatBundle includes sentences for object management, that include creating/modifying content as a pre-condition for other tests, as well as asserts for verifying that content is in the correct state after the execution of other any other steps.

While each trait adds feature sentences, most of the implementation logic is done at each ObjectManager class (see [ObjectManager/](../ObjectManager/) ).
The wanted object manager instance can be accessed by using the `get<Name>Manager()` magic method, like in the following example:
```php
    /**
     * @Given there is a Content Type Group with identifier :identifier
     */
    public function ensureContentTypeGroupExists( $identifier )
    {
        return $this->getContentTypeGroupManager()->ensureContentTypeGroupExists( $identifier );
    }
```

#### Testing
For each trait, a corresponding .feature file exists with scenarios using the implemented sentences.
This is not the equivalent of a fully-fledged test suite (such as phpunit unit testing), but serves the purposes of: Showcasing existing features/sentences with examples, making sure steps are correctly matched, basic sanity checks, as well as verifying that the behavior is the intended one.

In order to test BehatBundle features, it is necessary to run behat command with BehatBundle's `behat.yml` configuration file:

    $ bin/behat -c vendor/ezsystems/behatbundle/EzSystems/BehatBundle/behat.yml


The following sentences for object management have been implemented; for further information, please refer to traits found in [Context/Object/](../Context/Object/).


######  User Group

    Given there is a User Group with name :name
    Given there isn't a User Group with name :name
    Given there is a User Group with name :childGroupName in :parentGroup group
    Given there isn't a User Group with name :childGroupName in :parentGroup group
    Given there is a User Group with id :id
    Given there isn't a User Group with id :id
    Given there is a User Group with name :name with id :id in :parentGroup group
    Given there are the following User Groups:
    Given a User Group with name :name already exists
    Then User Group with name :name exists
    Then User Group with name :name doesn't exist
    Then User Group with name :name exists in group :parentGroup
    Then User Group with name :name exists in :parentGroup group
    Then User Group with name :name doesn't exist in group :parentGroup
    Then User Group with name :name doesn't exist in :parentGroup group


######  User

    Given there is a User with name :username
    Given there is a User with name :username, email :email and password :password
    Given there is a User with name :username in :parentGroup group
    Given there is a User with name :username, email :email and password :password in :parentGroup group
    Given there is a User with name :username with the following fields:
    Given there isn't a User with name :username
    Given there isn't a User with name :username in :parentGroup group
    Given there is a User with id :id
    Given there isn't a User with id :id
    Given there is a User with name :username with id :id in :parentGroup group
    Given there are the following Users:
    Given a User with name :username already exists
    Then User with name :username exists
    Then User with name :username doesn't exist
    Then User with name :username exists in group :parentGroup
    Then User with name :username exists in :parentGroup group
    Then User with name :username doesn't exist in group :parentGroup
    Then User with name :username doesn't exist in :parentGroup group
    Then User with name :username doesn't exist in the following groups:
    Then User with name :username has the following fields:
    Then User with name :username exists with the following fields:


###### Content Type Group

    Given there is a Content Type Group with identifier :identifier
    Given there isn't a Content Type Group with identifier :identifier
    Given there is Content Type Group with id :id
    Given there isn't a Content Type Group with id :id
    Given there is a Content Type Group with id :id and identifier :identifier
    Given there are the following Content Type Groups:
    Then Content Type Group with id :id exists
    Then Content Type Group with identifier :identifier exists
    Then Content Type Group with identifier :identifier was created
    Then Content Type Group with identifier :identifier wasn't deleted
    Then Content Type Group with id :id doesn't exist
    Then Content Type Group with identifier :identifier doesn't exist (anymore)
    Then Content Type Group with identifier :identifier wasn't created
    Then Content Type Group with identifier :identifier was deleted
    Then only :total Content Type Group(s) with identifier :identifier exists

