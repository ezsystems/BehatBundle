Feature: Multirepository setup for testing

    @multirepository
  Scenario: Set up new repository and make the default one unaccessible
    Given I add a new database connection called "second_connection"
    And I append configuration to "doctrine.dbal.connections.default" in "app/config/config.yml"
      """
        password: ThisPasswordIsIncorrect
      """
    And I set configuration to "ezpublish.repositories.new_repository"
        """
                storage:
                    engine: 'legacy'
                    connection: second_connection
                    config: {}
                search:
                    connection: second_connection
        """
    And I append configuration to "doctrine.dbal.connections.default" in "app/config/config.yml"
      """
          password: ThisPasswordIsIncorrect
      """
    And I set configuration to "admin_group" siteaccess
      | key                          | value          |
      | repository                   | new_repository |
    And I set configuration to "site" siteaccess
      | key                          | value          |
      | repository                   | new_repository |
