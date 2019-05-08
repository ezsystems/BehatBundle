Feature: Example scenarios showing how to use steps involving Languages, Content Types and Content Items

  @admin
  Scenario Outline: Create a language, Content Type and Content Items
  Given Language "Polski" with code "pol-PL" exists
  And I create a "<contentTypeName>" Content Type in "Content" with "<contentTypeIdentifier>" identifier
  | Field Type  | Name         | Identifier        | Required | Searchable | Translatable |
  | Text line   | Name         | name	           | yes      | yes	       | yes          |
  | <fieldType> | TestedField  | testedfield       | yes      | no	       | yes          |
  And I create "Folder" Content items in root in "pol-PL"
    | name              | short_name          |
    | <contentTypeName> | <contentTypeName>   |
  And I create 50 "<contentTypeIdentifier>" Content items in "<contentTypeName>" in "pol-PL"

    Examples:
    | contentTypeName      | contentTypeIdentifier | fieldType                    |
    | RichText CT          | RichTextCT            | Rich text                    |
    | URL CT               | URLCT                 | URL                          |
    | Email CT             | EmailCT               | Email address                |
    | Textline CT          | TextlineCT            | Text line                    |
    | ISBN CT              | IsbnCT                | ISBN                         |
    | Authors CT           | AuthorsCT             | Authors                      |
    | Text block CT        | TextBlockCT           | Text block                   |
    | Checkbox CT          | CheckboxCT            | Checkbox                     |
    | Country CT           | CountryCT             | Country                      |
    | Date CT              | DateCT                | Date                         |
    | Time CT              | TimeCT                | Time                         |
    | Float CT             | FloatCT               | Float                        |
    | Integer CT           | Integer               | Integer                      |
    | Map location CT      | MapLocationCT         | Map location                 |
    | Date and time CT     | DateAndTimeCT         | Date and time                |
    | Content relation CT  | ContentRelationCT     | Content relation (single)    |
    | Content relations CT | ContentRelationsCT    | Content relations (multiple) |
    | Image CT             | ImageCT               | Image                        |
    | File CT              | FileCT                | File                         |
    | Media CT             | MediaCT               | Media                        |
