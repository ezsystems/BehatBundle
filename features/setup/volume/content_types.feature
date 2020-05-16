Feature: Create Content Types required for volume testing

  @admin @contentTypes
  Scenario: Create Content Types used in volume testing with Workflow 
    Given I create a "LongArticle" Content Type in "Content" with "volume_long_article" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Rich text                    | Content1          | content1    | no       | yes        | yes          |
      | Image                        | Image             | image       | yes      | no         | no           |
      | Rich text                    | Content2          | content2    | no       | yes        | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |
    And I create a "ShortArticle" Content Type in "Content" with "volume_short_article" identifier
      | Field Type  | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line   | Name              | name	      | no       | yes	      | yes          |
      | Text line   | Short description | short       | no       | yes        | yes          |
      | Rich text   | Content           | content     | no       | yes        | yes          |
    And I create a "LandingPage" Content Type in "Content" with "volume_page" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Landing Page                 | Page              | page        | yes      | no         | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |
    And I create a "Form" Content Type in "Content" with "volume_form" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Rich text                    | Content           | content     | no       | yes        | yes          |
      | Form                         | Form              | form        | yes      | no         | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |

    @admin @contentTypes
  Scenario: Create Content Types used in volume testing without Workflow
    Given I create a "LongArticleNoWorkflow" Content Type in "Content" with "volume_long_article_no_workflow" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Rich text                    | Content1          | content1    | no       | yes        | yes          |
      | Image                        | Image             | image       | yes      | no         | no           |
      | Rich text                    | Content2          | content2    | no       | yes        | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |
    And I create a "ShortArticleNoWorkflow" Content Type in "Content" with "volume_short_article_no_workflow" identifier
      | Field Type  | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line   | Name              | name	      | no       | yes	      | yes          |
      | Text line   | Short description | short       | no       | yes        | yes          |
      | Rich text   | Content           | content     | no       | yes        | yes          |
    And I create a "LandingPageNoWorkflow" Content Type in "Content" with "volume_page_no_workflow" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Landing Page                 | Page              | page        | yes      | no         | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |
    And I create a "FormNoWorkflow" Content Type in "Content" with "volume_form_no_workflow" identifier
      | Field Type                   | Name              | Identifier  | Required | Searchable | Translatable |
      | Text line                    | Name              | name	       | no       | yes	       | yes          |
      | Text line                    | Short description | short       | no       | yes        | yes          |
      | Rich text                    | Content           | content     | no       | yes        | yes          |
      | Form                         | Form              | form        | yes      | no         | yes          |
      | Content relations (multiple) | Similar items     | similar     | yes      | yes        | no           |