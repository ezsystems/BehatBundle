Feature: Multirepository setup for testing

    @multirepository
  Scenario: Set up new repository and make the default one unaccessible
    Given I set configuration to "doctrine.dbal" in "config/packages/doctrine.yaml"
    """
        default_connection: default
        connections:
            default:
                url: 'mysql://INVALID:INVALID@127.0.0.1/INVALID'
            second_connection:
                url: '%env(resolve:DATABASE_URL)%'
    """
    And I set configuration to "ibexa.repositories.new_repository"
        """
                storage:
                    engine: 'legacy'
                    connection: second_connection
                    config: {}
                search:
                    connection: second_connection
        """
    And I set configuration to "admin_group" siteaccess
      | key                          | value          |
      | repository                   | new_repository |
    And I set configuration to "site" siteaccess
      | key                          | value          |
      | repository                   | new_repository |
