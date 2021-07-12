Feature: Role with location limitation to deleted location

  @admin @setup @deletedLocation
  Scenario: Create a Role and assign policy with limitation pointing to deleted location
    Given I create "folder" Content items in root in "eng-GB"
      | name            | short_name      |
      | DeletedLocation | DeletedLocation |
    And I create a role "DeletedLocationRole"
    And I add policy "content" "read" to "DeletedLocationRole" with limitations
      | limitationType | limitationValue  |
      | Location       | /DeletedLocation |
    And I send "/DeletedLocation" to the Trash
