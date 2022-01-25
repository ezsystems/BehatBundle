Feature: Email provider setup for testing

    @test
  Scenario: Set up both username and email providers
    Given I set configuration to "security.providers.ibexa" in "config/packages/security.yaml"
    """
        chain:
            providers: [ ibexa_email, ibexa_username ]
    """
    And I append configuration to "security.providers" in "config/packages/security.yaml"
    """
        ibexa_email:
            id: Ibexa\Core\MVC\Symfony\Security\User\EmailProvider
        ibexa_username:
            id: Ibexa\Core\MVC\Symfony\Security\User\UsernameProvider
    """
    And I append configuration to "security.firewalls.ibexa_front" in "config/packages/security.yaml"
    """
        provider: ibexa
    """
