Feature: Content Type Creation
    As a behat developer
    I need to run Content Type scenarios
    In order to test Content Type creation functions

    Scenario: Test that a content type exists in a Group
        Given a Content Type exists with identifier "Test1" in Group with identifier "Content" with fields:
            | Identifier | Type     | Name  |
            | title      | ezstring | Title |
        Then Content Type with identifier "Test1" exists in Group with identifier "Content"

    Scenario: Test a content type exits
        Given a Content Type exists with identifier "Test2" with fields:
            |   Identifier   |     Type       |     Name      |
            |  title         |  ezstring      |  Title        |
        Then Content Type with identifier "Test2" exists
