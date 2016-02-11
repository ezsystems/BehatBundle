Feature: Role Creation
    As a behat developer
    I need to run Role scenarios
    In order to test Role creation functions

    Scenario: Test that a role exists
        Given a "Test" role exists
        Then I see that a "Test" role exists
