Feature: Test various steps

  Scenario:
    Given I create 5 "Folder" Content items in "Home"
    And I create "Folder" Content items in "Home"
       | name  |
       | test1 |
       | test2 |
    And I create a "Marek" Content Type with "marek" identifier
      | Field Type   | Name              | Identifier        | Required | Searchable | Translatable |
      | Text line	 | Name              | name	             | yes      | yes	     | yes          |
      | Text line	 | Short Description | short_description | yes      | yes	     | yes          |
      | Image	     | Photo	         | photo	         | yes      | no	     | no           |
      | RichText	 | Full Description	 | description       | yes      | yes	     | yes          |
    And I create a role "marekRole1"
    And I create a role "marekRole2" with policies
      | policyName      |
      | content/read    |
      | content/publish |
    And I add policies to "marekRole1"
       | policyName      |
       | content/read    |
       | content/publish |
    And I add policy "content/read" to "marekRole1" with limitations
      | limitationType | limitationValue |
      | owner          | self            |
    And I create a user "Marek"
    And I create a user "Marek2" in group "Nocon"
    And I assign user group "Marek" to role "marekRole1"
    And I assign user group "Nocon" to role "marekRole2"




