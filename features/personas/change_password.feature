Feature: User that is allowed to change password

  @admin @setup @changePassword
  Scenario: Create a Role and assign policies allowing to change password
    Given I create a user group "ChangePasswordGroup"
    And I create a user "UserPassword" with last name "Change" in group "ChangePasswordGroup"
    And I create a role "ChangePassword" with policies
      | module      | function |
      | user        | login    |
      | user        | password |
    And I assign user group "ChangePasswordGroup" to role "ChangePassword"
