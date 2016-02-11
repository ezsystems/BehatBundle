Feature: FieldTypes Creation
    As a behat developer
    I need to run FieldTypes scenarios
    In order to test FieldTypes creation functions

    Scenario: Test that a integer field exist
        Given a Content Type with an "integer" field exists
        And a Content of this type exists with "integer" Field Value set to "5"
