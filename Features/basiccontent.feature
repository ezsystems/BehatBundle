Feature: Basic Content Creation
    As a behat developer
    I need to run Role scenarios
    In order to test Basic Content creation functions

    Scenario: Test that a basic folder is created
        Given a "TestA" folder exists
        Then a "TestB/TestC" folder exists

    Scenario: Test that a basic article is created
        Given a "TestD" article exists
        Then a "TestE/TestF" article exists
