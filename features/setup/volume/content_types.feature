Feature: Create Content Types required for volume testing

  @admin @contentTypes
  Scenario: Create Content Types used in volume testing
    Given I create a "LongArticle" Content Type in "Content" with "long_article" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Rich text                    | Content1          | content1    | no       | yes        | yes          |
      | Image                        | Image             | image       | yes      | no         | yes          |
      | Rich text                    | Content2          | content2    | no       | yes        | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | yes          |
    And I create a "ShortArticle" Content Type in "Content" with "short_article" identifier
      | Field Type  | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line   | Name              | name	      | no       | yes	      | yes          |
      | Text line   | Short description | short       | no       | yes        | yes          |
      | Rich text   | Content           | content     | no       | yes        | yes          |
