Feature: Editor user that has policies with Content Type limitation

  @admin @setup @addLocation
  Scenario: Create a Role and assign policies with Content Type limitation
    Given I create "folder" Content items in root in "eng-GB"
      | name        | short_name  | short_description | description |
      | Destination | Destination | Destination       | Destination |
    And I create a user group "AddLocationGroup"
    And I create a user "Add" with last name "Location" in group "AddLocationGroup"
    And I create a role "AddLocationRole" with policies
      | module | function |
      | user   | login    |
      | content | read    |
    And I add policy "content" "create" to "AddLocationRole" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "edit" to "AddLocationRole" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "publish" to "AddLocationRole" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "versionread" to "AddLocationRole" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I add policy "content" "manage_locations" to "AddLocationRole" with limitations
      | limitationType | limitationValue |
      | ContentType    | Article         |
    And I assign user group "AddLocationGroup" to role "AddLocationRole"

  @setup @addLocation
  Scenario: Create new article as "Add Location" user
    Given I am using the API as "Add"
    And I create "article" Content items in root in "eng-GB"
      | title      | short_title | intro      |
      | NewArticle | NewArticle  | NewArticle |
