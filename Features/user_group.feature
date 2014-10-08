Feature: User group Creation
    As a behat developer
    I need to run user group scenarios
    In order to test user group creation functions

    Scenario: Test that a group already exists
        Given a User Group with name "Members" already exists
        Then User Group with name "Members" exists

    Scenario: Test that a group does not exist
        Given there isn't a User Group with name "testUserGroup"
        Then User Group with name "testUserGroup" doesn't exist

    Scenario: test creation of user group
        Given there is a User Group with name "newUserGroup"
        Then User Group with name "newUserGroup" exists

    Scenario: test creation of user group in existing parent group
        Given a User Group with name "Members" already exists
        And there is a User Group with name "testUserGroup" in "Members" group
        Then User Group with name "testUserGroup" exists
        And User Group with name "testUserGroup" exists in "Members" group

    Scenario: test creation of user group in new parent group
        Given there is a User Group with name "test Child Group" in "Test Parent Group" group
        Then User Group with name "Test Parent Group" exists
        Then User Group with name "test Child Group" exists

    Scenario: test creation of user groups from table
        Given there are the following User Groups:
            | childGroup      | parentGroup      |
            | testUserGroup1  | Members          | # should create.
            | testUserGroup1  | Editors          | # should create.
            | testUserGroup3  | Test Parent      | # parent and child should be created.
            | innerGroup3-1   | testUserGroup3   | # should be created inside previous.
        Then User Group with name "testUserGroup1 exists
        And User Group with name "testUserGroup1" exists in group "Members"
        And User Group with name "testUserGroup1" exists in group "Editors"
        And User Group with name "Test Parent" exists
        And User Group with name "testUserGroup3" exists in group "Test Parent"
        And User Group with name "innerGroup3-1" exists in group "testUserGroup3"