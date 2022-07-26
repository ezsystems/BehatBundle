Feature: Language configuration for testing translations

  @admin @setup @translation
  Scenario: Add French language. Add it to admin panel configuration.
    Given Language "French" with code "fre-FR" exists
    And I append configuration to "admin_group" siteaccess in "config/packages/ezplatform_admin_ui.yaml"
      | key       | value  |
      | languages | fre-FR |
