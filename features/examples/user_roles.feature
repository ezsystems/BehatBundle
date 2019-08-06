Feature: Example scenarios showing how to use steps involving Roles and Users

  @admin
  Scenario: Create a Role and assign policies with limitations to it
    And I create a role "simpleRole"
    And I create a role "simpleRole" with policies
      | module      | function |
      | content     | read     |
      | content     | publish  |
    And I add policies to "simpleRole"
      | module      | function |
      | section     | assign   |
      | user        | login    |

  @admin
  Scenario: Create a Role and assign policies with limitations to it
    Given Language "Polski" with code "pol-PL" exists
    And Object State Group "Colors" with identifier "colors" exists
      | objectStates |
      | red          |
      | green        |
    And I create a role "complexRole"
    And I add policy "content" "create" to "complexRole" with limitations
      | limitationType      | limitationValue         |
      | Subtree             | /Media,/Media/Images    |
      | Content Type        | Article,Folder          |
      | Language            | pol-PL,eng-GB           |
      | Location            | /Media,/Media/Images    |
      | Parent Content Type | Folder,Article          |
      | Parent Depth        | 5                       |
      | Parent Owner        | self                    |
      | Parent Group        | self                    |
    And I add policy "content" "read" to "complexRole" with limitations
      | limitationType      | limitationValue         |
      | State               | colors:green,colors:red |
      | Owner               | self                    |
      | Group               | self                    |
      | Section             | Media,Standard          |
    And I add policy "user" "login" to "complexRole" with limitations
      | limitationType      | limitationValue         |
      | Siteaccess          | site,admin              |
    And I add policy "section" "assign" to "complexRole" with limitations
      | limitationType      | limitationValue         |
      | New Section         | Media,Standard          |
    And I add policy "state" "assign" to "complexRole" with limitations
      | limitationType      | limitationValue         |
      | New State           | colors:green,colors:red |

  @admin
  Scenario: Create Users, User groups and assign them to Roles
    Given I create a user group "MyGroup"
    And I create a user "TestUser"
    And I create a user "AnotherTestUser" in group "MyGroup"
    And I assign user "TestUser" to role "simpleRole"
    And I assign user group "MyGroup" to role "complexRole"

