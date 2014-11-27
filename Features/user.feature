Feature: User Creation
    As a behat developer
    I need to run user scenarios
    In order to test user creation/verification functions

    Scenario: Test that a user already exists
        Given there is a User with name "Anonymous"
        Then User with name "Anonymous" exists

    Scenario: Test creating a new user
        Given there is a User with name "testuser"
        Then User with name "testuser" exists

    Scenario: Test that a user does not exist
        Given there isn't a User with name "testUser"
        Then User with name "testUser" doesn't exist

    Scenario: Test creating a new user in specific group
        Given there is a User with name "testuser" in "Members" group
        Then User with name "testuser" exists in "Members" group

    Scenario: Test that a user does not exist in groups
        Given there isn't a User with name "testUser"
        And there is a User with name "testuser" in "Members" group
        Then User with name "testuser" exists
        And User with name "testuser" exists in "Members" group
        And User with name "testuser" doesn't exist in the following groups:
            | parentGroup           |
            | Partners              |
            | Anonymous Users       |
            | Editors               |
            | Administrator users   |

    Scenario: Test that a user does not exist in other group
        Given there isn't a User with name "testUser"
        And there is a User with name "testuser" in "Members" group
        Then User with name "testuser" exists
        And User with name "testuser" exists in "Members" group
        And User with name "testuser" doesn't exist in "Some other group" group

    Scenario:
        Given there isn't a User with name "testuser"
        Given there is a User with name "testuser" with the following fields:
            | Name          | value           |
            | email         | testuser@ez.no  |
            | password      | testuser        |
            | first_name    | Test            |
            | last_name     | User            |
        Then User with name "testuser" exists
        And User with name "testuser" has the following fields:
            | Name          | value           |
            | email         | testuser@ez.no  |
            | password      | testuser        |
            | first_name    | Test            |
            | last_name     | User            |
