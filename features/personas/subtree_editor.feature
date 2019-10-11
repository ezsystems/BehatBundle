Feature: Editor that has access only to a Subtree of Content Structure

  @admin @setup @subtreeEditor
  Scenario: Create a Role and assign policies with limitations to it
    Given I create a "DedicatedFolder" Content Type in "Content" with "dedicatedFolder" identifier
      | Field Type  | Name         | Identifier        | Required | Searchable | Translatable |
      | Text line   | Name         | name	           | yes      | yes	       | yes          |
      | Text line   | Short name   | short_name        | yes      | no	       | yes          |
    And I create "DedicatedFolder" Content items in root in "eng-GB"
      | name              | short_name        |
      | FolderGrandParent | FolderGrandParent |
    And I create "DedicatedFolder" Content items in "FolderGrandParent" in "eng-GB"
      | name         | short_name   |
      | FolderParent | FolderParent |
    And I create "DedicatedFolder" Content items in "FolderGrandParent/FolderParent" in "eng-GB"
      | name         | short_name   |
      | FolderChild1 | FolderChild1 |
      | FolderChild2 | FolderChild2 |
    And I create "DedicatedFolder" Content items in "FolderGrandParent/FolderParent/FolderChild1" in "eng-GB"
      | name          | short_name    |
      | ContentToMove | ContentToMove |
    And I create a user group "SubtreeEditorsGroup"
    And I create a user "SubtreeEditor" with last name "Subtree Editor" in group "SubtreeEditorsGroup"
    And I create a role "BasicRole" with policies
      | module      | function   |
      | user        | login      |
    And I create a user "ThisUserIsAReviewer" with last name "Test" in group "SubtreeEditorsGroup"
    And I add policy "content" "read" to "BasicRole" with limitations
      | limitationType      | limitationValue                                    |
      | Location            | Users/SubtreeEditorsGroup/ThisUserIsAReviewer Test |
    And I create a role "SubtreeEditorsRole" with policies
      | module      | function           |
      | content     | read               |
      | content     | create             |
      | content     | publish            |
      | content     | remove             |
      | content     | edit               |
      | section     | view               | 
      | content     | versionread        |
      | content     | reverserelatedlist |
    And I create a role "ReadNodesForSubtreeEditors"
    And I add policy "content" "read" to "ReadNodesForSubtreeEditors" with limitations
      | limitationType      | limitationValue         |
      | Location            | root,/FolderGrandParent |
    And I add policy "content" "versionread" to "ReadNodesForSubtreeEditors" with limitations
      | limitationType      | limitationValue         |
      | Location            | root,/FolderGrandParent |
    And I assign user group "SubtreeEditorsGroup" to role "BasicRole"
    And I assign user group "SubtreeEditorsGroup" to role "ReadNodesForSubtreeEditors"
    And I assign user group "SubtreeEditorsGroup" to role "SubtreeEditorsRole" with limitations:
      | limitationType      | limitationValue                 |
      | Subtree             | /FolderGrandParent/FolderParent |
