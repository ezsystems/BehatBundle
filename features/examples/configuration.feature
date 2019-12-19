Feature: Example scenarios showing how to set configuration

  @admin
  Scenario: Add a language. Create a siteaccess using it.
    Given Language "Polski" with code "pol-PL" exists
    And I add a siteaccess "pol" to "site_group" with settings
     | key       | value         |
     | languages | pol-PL,eng-GB |
    And I append configuration to "admin_group" siteaccess
      | key                          | value  |
      | languages                    | pol-PL |

  Scenario: Configure Varnish as http cache
    Given I set configuration to "ezpublish.http_cache"
    """
        purge_type: 'http'
    """
    And  I append configuration to "default" siteaccess under "http_cache" key
    """
        purge_servers: ['http://my_purge_server']
    """
