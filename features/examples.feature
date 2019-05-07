Feature: Example scenarios showing how to use steps from this bundle

  @admin
  Scenario: Create a language and Content Items
    Given Language "Polski" with code "pol-PL" exists
    And I create 500 "Folder" Content items in root in "pol-PL"
    And I create "Folder" Content items in root in "eng-GB"
       | name  | short_name |
       | test1 | test1_sn   |
       | test2 | test2_sn   |

  @admin
  Scenario: Create a ContentType
    Given I create a "TestContentType" Content Type in "Content" with "my_ct" identifier
      | Field Type | Name              | Identifier        | Required | Searchable | Translatable |
      | Text line  | Name              | name	           | yes      x| yes	       | yes          |
      | Text line  | Short Description | short_description | yes      | yes	       | yes          |
      | Image	   | Photo	           | photo	           | yes      | no	       | no           |
      | Rich text  | Full Description  | description       | yes      | yes	       | yes          |

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

