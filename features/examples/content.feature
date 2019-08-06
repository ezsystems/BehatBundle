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

  @admin
  Scenario Outline: Create a Content item and edit specified field
    Given I create a "<contentTypeName>" Content Type in "Content" with "<contentTypeIdentifier>" identifier
      | Field Type  | Name         | Identifier        | Required | Searchable | Translatable |
      | Text line   | Name         | name	           | yes      | yes	       | yes          |
      | <fieldType> | TestedField  | testedfield       | yes      | no	       | yes          |
    And I create <contentTypeIdentifier> Content items in root in "eng-GB"
      | name              |
      | <contentTypeName> |
    And I create "Folder" Content items in root in "eng-GB"
      | name              | short_name      |
      | RelationFolder1   | RelationFolder1 |
      | RelationFolder2   | RelationFolder2 |
    When I edit "<contentTypeName>" Content item in "eng-GB"
      | testedfield  |
      | <valueToSet> |

  Examples:
       | contentTypeName       | contentTypeIdentifier | fieldType                    | valueToSet                                                                  |
       | RichText CT2          | RichTextCT2           | Rich text                    | EditedField                                                                 |
       | URL CT2               | URLCT2                | URL                          | www.ez.no                                                                   |
       | Email CT2             | EmailCT2              | Email address                | nospam@ez.no                                                                |
       | Textline CT2          | TextlineCT2           | Text line                    | TestTextLine                                                                |
       | ISBN CT2              | IsbnCT2               | ISBN                         | 9783161484100                                                               |
       | Authors CT2           | AuthorsCT2            | Authors                      | AuthorName,nospam@ez.no                                                     |
       | Text block CT2        | TextBlockCT2          | Text block                   | TestTextBlock                                                               |
       | Checkbox CT2          | CheckboxCT2           | Checkbox                     | true                                                                        |
       | Country CT2           | CountryCT2            | Country                      | FR                                                                          |
       | Date CT2              | DateCT2               | Date                         | 2018-12-31                                                                  |
       | Time CT2              | TimeCT2               | Time                         | 13:55:00                                                                    |
       | Float CT2             | FloatCT2              | Float                        | 2.34                                                                        |
       | Integer CT2           | Integer2              | Integer                      | 10                                                                          |
       | Map location CT2      | MapLocationCT2        | Map location                 | Tokio                                                                       |
       | Date and time CT2     | DateAndTimeCT2        | Date and time                | 2018-12-31 13:55:00                                                         |
       | Content relation CT2  | ContentRelationCT2    | Content relation (single)    | /RelationFolder1                                                            |
       | Content relations CT2 | ContentRelationsCT2   | Content relations (multiple) | RelationFolder1,/RelationFolder2                                            |
       | Image CT2             | ImageCT2              | Image                        | /var/www/vendor/ezsystems/behatbundle/src/lib/Data/Images/small1.jpg |
       | File CT2              | FileCT2               | File                         | /var/www/vendor/ezsystems/behatbundle/src/lib/Data/Files/file1.txt   |
       | Media CT2             | MediaCT2              | Media                        | /var/www/vendor/ezsystems/behatbundle/src/lib/Data/Videos/video1.mp4 |
