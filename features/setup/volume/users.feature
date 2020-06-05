Feature: Create languages required for volume testing

  @admin @users
  Scenario Outline: Create users for specific regions
    Given I create a user group "<groupName>"
    And I create a user "<userName>1" with last name "<userName>1" in group "<groupName>"
    And I create a user "<userName>2" with last name "<userName>2" in group "<groupName>"
    And I create a user "<userName>3" with last name "<userName>3" in group "<groupName>"
    And I create a user "<userName>4" with last name "<userName>4" in group "<groupName>"
    And I create a user "<userName>5" with last name "<userName>5" in group "<groupName>"
    And I create a user "<userName>6" with last name "<userName>6" in group "<groupName>"
    And I create a user "<userName>7" with last name "<userName>7" in group "<groupName>"
    And I create a user "<userName>8" with last name "<userName>8" in group "<groupName>"
    And I create a user "<userName>9" with last name "<userName>9" in group "<groupName>"
    And I create a user "<userName>10" with last name "<userName>10" in group "<groupName>"
    And I create a role "<roleName>Basic" with policies
    | module      | function           |
    | user        | login              |
    And I add policy "content" "read" to "<roleName>Basic" with limitations
    | limitationType      | limitationValue |
    | Location            | root,/Europe    |
    And I add policy "content" "versionread" to "<roleName>Basic" with limitations
    | limitationType      | limitationValue     |
    | Location            | root,/Europe        |
    And I assign user group "<groupName>" to role "<roleName>Basic"
    And I create a role "<roleName>Limited" with policies
      | module      | function           |
      | content     | read               |
      | content     | versionread        |
      | content     | create             |
      | content     | publish            |
      | content     | remove             |
      | content     | edit               |
      | section     | view               |
      | content     | reverserelatedlist |
      | workflow    | change_stage      |
    And I assign user group "<groupName>" to role "<roleName>Limited" with limitations:
      | limitationType      | limitationValue        |
      | Subtree             | /Europe/<rootItemName> |

    Examples:
    | groupName         | userName         |  roleName            | rootItemName |
    | FrenchEditors     | FrenchEditor     | FrenchEditorsRole    | France       |
    | GermanEditors     | GermanEditor     | GermanEditorsRole    | Germany      |
    | PolishEditors     | PolishEditor     | PolishEditorsRole    | Poland       |
    | EnglishEditors    | EnglishEditor    | EnglishEditorsRole   | England      |
    | ItalianEditors    | ItalianEditor    | ItalianEditorsRole   | Italy        |
    | SpanishEditors    | SpanishEditor    | SpanishEditorsRole   | Spain        |
    | MalteseEditors    | MalteseEditor    | MalteseEditorsRole   | Malta        |
    | SwissEditors      | SwissEditor      | SwissEditorsRole     | Switzerland  |
    | AustrianEditors   | AustrianEditor   | AustrianEditorROle   | Austria      |
    | PortugueseEditors | PortugueseEditor | PortugueseEditorRole | Portugal     |
    | UkrainianEditors  | UkrainianEditor  | UkrainianEditorRole  | Ukraine      |
    | SwedishEditors    | SwedishEditor    | SwedishEditorRole    | Sweden       |
    | NorwegianeEditors | NorwegianEditor  | NorwegianEditorRole  | Norway       |
    | FinnishEditors    | FinnishEditor    | FinnishEditorRole    | Finland      |
    | DanishEditors     | DanishEditor     | DanishEditorRole     | Denmark      |
    | CroatianEditors   | CroatianEditor   | CroatianEditorRole   | Croatia      |
