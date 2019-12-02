Feature: Set up basic Compound Map\URI nad Map\Host SiteAccess matching configuration

  @admin
  Scenario: Add a language. Create a siteaccess using it and add it to PageBuilder
    Given Language "Polski" with code "pol-PL" exists
    And I add a siteaccess "test" to "site_group" with settings
      | key       | value  |
      | languages | pol-PL,eng-GB |
    And I "set" siteaccess matcher configuration
    """
    Compound\LogicalAnd:
        site:
            matchers:
                Map\URI:
                    st: true
                Map\Host:
                    site.example.com: true
            match: site
        test:
            matchers:
                Map\URI:
                    tst: true
                Map\Host:
                    test.example.com: true
            match: test
        admin:
            matchers:
                Map\URI:
                    admn: true
                Map\Host:
                    admin.example.com: true
            match: admin
    """
    And I append configuration to "admin_group" siteaccess
      | key                          | value  |
      | languages                    | pol-PL |
      | page_builder.siteaccess_list | test   |
