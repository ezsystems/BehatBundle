Feature: Test various steps

  Scenario:
     Given Language "Polski" with code "pol-PL" exists
    Given I create 5 "Folder" Content items in root in "pol-PL"
    And I create "Folder" Content items in root in "eng-GB"
       | name  | short_name |
       | test1 | test1_sn   |
       | test2 | test2_sn   |
    And I create a "Marek" Content Type in "Content" with "marek" identifier
      | Field Type   | Name              | Identifier        | Required | Searchable | Translatable |
      | Text line	 | Name              | name	             | yes      | yes	     | yes          |
      | Text line	 | Short Description | short_description | yes      | yes	     | yes          |
      | Image	     | Photo	         | photo	         | yes      | no	     | no           |
      | RichText	 | Full Description	 | description       | yes      | yes	     | yes          |
    And I create a role "marekRole1"
    And I create a role "marekRole2" with policies
      | module      | function |
      | content     | read     |
      | content     | publish  |
    And I add policies to "marekRole1"
      | module      | function |
      | content     | read     |
      | content     | publish  |
    And I add policy "content" "read" to "marekRole1" with limitations
      | limitationType    | limitationValue |
      | Subtree           | /               |
      | Subtree           | /Media          |
      | Class             | Folder          |
      | Class             | Article         |
    And I create a user group "Nocon"
    And I create a user "Marek"
    And I create a user "Marek3" in group "Nocon"
    And I assign user "Marek" to role "marekRole1"
    And I assign user group "Nocon" to role "marekRole2"




