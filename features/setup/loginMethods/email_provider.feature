Feature: Email provider setup for testing

  Scenario: Set up both username and email providers
    Given I set configuration to "security.providers.ezplatform" in "config/packages/security.yaml"
    """
        chain:
            providers: [ ibexa_email, ibexa_username ]
    """
    And I append configuration to "security.providers" in "config/packages/security.yaml"
    """
        ibexa_email:
            id: ezpublish.security.user_provider.email
        ibexa_username:
            id: ezpublish.security.user_provider.username
    """
    And I append configuration to "security.firewalls.ezpublish_front" in "config/packages/security.yaml"
    """
        provider: ezplatform
    """
