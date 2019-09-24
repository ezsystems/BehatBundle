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
            | password      | PassWord42      |
            | first_name    | Test            |
            | last_name     | User            |
        Then User with name "testuser" exists
        And User with name "testuser" has the following fields:
            | Name          | value           |
            | email         | testuser@ez.no  |
            | password      | PassWord42      |
            | first_name    | Test            |
            | last_name     | User            |

    @admin
    Scenario: Create a Role and assign policies with limitations to it
        Given I create a role "simpleRole"
        And I create a role "simpleRole" with policies
            | module      | function |
            | content     | read     |
            | content     | publish  |
        And I add policies to "simpleRole"
            | module      | function |
            | section     | assign   |
            | user        | login    |

    @admin
    Scenario: Create a Role and assign policies with limitations to it
        Given Language "Polski" with code "pol-PL" exists
        And Object State Group "Colors" with identifier "colors" exists
            | objectStates |
            | red          |
            | green        |
        And I create a role "complexRole"
        And I add policy "content" "create" to "complexRole" with limitations
            | limitationType      | limitationValue         |
            | Subtree             | /Media,/Media/Images    |
            | Content Type        | Article,Folder          |
            | Language            | pol-PL,eng-GB           |
            | Location            | /Media,/Media/Images    |
            | Parent Content Type | Folder,Article          |
            | Parent Depth        | 5                       |
            | Parent Owner        | self                    |
            | Parent Group        | self                    |
        And I add policy "content" "read" to "complexRole" with limitations
            | limitationType      | limitationValue         |
            | State               | colors:green,colors:red |
            | Owner               | self                    |
            | Group               | self                    |
            | Section             | Media,Standard          |
        And I add policy "user" "login" to "complexRole" with limitations
            | limitationType      | limitationValue         |
            | Siteaccess          | site,admin              |
        And I add policy "section" "assign" to "complexRole" with limitations
            | limitationType      | limitationValue         |
            | New Section         | Media,Standard          |
        And I add policy "state" "assign" to "complexRole" with limitations
            | limitationType      | limitationValue         |
            | New State           | colors:green,colors:red |

    @admin
    Scenario: Create Users, User groups and assign them to Roles
        Given I create a user group "MyGroup"
        And I create a user "TestUser" with last name "TestLastName"
        And I create a user "AnotherTestUser" with last name "TestLastName" in group "MyGroup"
        And I assign user "TestUser" to role "simpleRole"
        And I assign user group "MyGroup" to role "complexRole"