Feature: Content Type group Creation
    As a behat developer
    I need to run Content Type group scenarios
    In order to test Content Type group creation functions

    Scenario: Test that a group exists
        Given there is a Content Type Group with identifier "Class Group"
        Then Content Type Group with identifier "Class Group" exists

    Scenario: Test that a group does not exist
        Given there isn't a Content Type Group with identifier "testContentTypeGroup"
        Then Content Type Group with identifier "testContentTypeGroup" doesn't exist

    Scenario: test creation of Content Type group
        Given there is a Content Type Group with identifier "newContentTypeGroup"
        Then Content Type Group with identifier "newContentTypeGroup" was created

    Scenario: Test creation and removal of a Content Type group
        Given there is a Content Type Group with identifier "newContentTypeGroup"
        And there isn't a Content Type Group with identifier "newContentTypeGroup"
        Then Content Type Group with identifier "newContentTypeGroup" doesn't exist

    Scenario: test that a group exists with specific id
        Given there is Content Type Group with id "10"
        Then Content Type Group with id "10" exists

    Scenario: test that a group doesn't exist with specific id
        Given there isn't a Content Type Group with id "10"
        Then Content Type Group with id "10" doesn't exist

    Scenario: test creation of Content Type groups from table
        Given there are the following Content Type Groups:
            | group                 |
            | testContentTypeGroup1 |
            | testContentTypeGroup2 |
            | testContentTypeGroup3 |
        Then Content Type Group with identifier "testContentTypeGroup1" was created
        And Content Type Group with identifier "testContentTypeGroup2" was created
        And Content Type Group with identifier "testContentTypeGroup3" was created
