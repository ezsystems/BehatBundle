Feature: Example scenarios showing how to use steps involving Roles and Users

  @admin
  Scenario: Create a Role and assign policies to it
    Given I create a role "testRole1"
    And I create a role "testRole2" with policies
      | module      | function |
      | content     | read     |
      | content     | publish  |
    And I add policies to "testRole1"
      | module      | function |
      | content     | read     |
      | content     | publish  |
    And I add policy "content" "read" to "testRole1" with limitations
      | limitationType    | limitationValue |
      | Subtree           | /Media          |
      | Content Type      | Article         |

  @admin
  Scenario: Create Users, User groups and assign them to Roles
    Given I create a user group "MyGroup"
    And I create a user "TestUser"
    And I create a user "AnotherTestUser" in group "MyGroup"
    And I assign user "TestUser" to role "testRole1"
    And I assign user group "MyGroup" to role "testRole2"

