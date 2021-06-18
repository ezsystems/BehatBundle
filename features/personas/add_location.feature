Feature: Editor user that has policies with Content Type limitation

  @admin @setup @addLocation
  Scenario: Create a Role and assign policies with Content Type limitation
    Given I create "folder" Content items in root in "eng-GB"
      | name        | short_name  | short_description | description |
      | Destination | Destination | Destination       | Destination |
    And I create a user group "AddLocationGroup"
    And I create a user "Add" with last name "Location" in group "AddLocationGroup"
    And I create a role "AddLocation" with policies
      | module | function |
      | user   | login    |
      | content | read    |
    And I add policy "content" "create" to "AddLocation" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "edit" to "AddLocation" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "publish" to "AddLocation" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "versionread" to "AddLocation" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "manage_location" to "AddLocation" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I assign user group "AddLocationGroup" to role "AddLocation"

