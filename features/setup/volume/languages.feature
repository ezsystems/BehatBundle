Feature: Create languages required for volume testing

  @admin @language
  Scenario: Create languages used in volume testing
    Given Language "Polski" with code "pol-PL" exists
    And Language "French" with code "fre-FR" exists
    And Language "English" with code "eng-GB" exists
    And Language "German" with code "ger-DE" exists
    And Language "Italian" with code "ita-IT" exists
    And Language "Spanish" with code "esl-ES" exists
    And Language "Portuguese" with code "por-PT" exists
    And Language "Ukrainian" with code "ukr-UA" exists
    And Language "Swedish" with code "swe-SE" exists
    And Language "Norwegian" with code "nor-NO" exists
    And Language "Finnish" with code "fin-FI" exists
    And Language "Danish" with code "dan-DK" exists
    And Language "Croatian" with code "cro-HR" exists
