@customStyles
Feature: Example scenarios showing how to set custom styles configuration

  Scenario: Add Highlighted Block custom styles configuration
    Given I "append" configuration to "ibexa_fieldtype_richtext.custom_styles"
    """
        highlighted_block_test:
            template: '@ibexadesign/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_block.html.twig'
            inline: false
    """
    And I "append" configuration to "default" siteaccess under "fieldtypes.ezrichtext.custom_styles" key
    """
        highlighted_block_test
    """
    And I create a file "templates/themes/standard/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_block.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_block.html.twig"
    And I create a file "templates/themes/admin/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_block.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_block.html.twig"
    And I create a file "translations/custom_styles.en.yaml" with content from "Files/Richtext/CustomStyle/translations/highlighted_block.en.yaml"

  Scenario: Add Highlighted Word custom styles configuration
    Given I "append" configuration to "ibexa_fieldtype_richtext.custom_styles"
    """
        highlighted_word_test:
            template: '@ibexadesign/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_word.html.twig'
            inline: true
    """
    And I "append" configuration to "default" siteaccess under "fieldtypes.ezrichtext.custom_styles" key
    """
        highlighted_word_test
    """
    And I create a file "templates/themes/standard/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_word.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_word.html.twig"
    And I create a file "templates/themes/admin/field_type/ibexa_fieldtype_richtext/custom_style/highlighted_word.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_word.html.twig"
    And I append to "translations/custom_styles.en.yaml" file "Files/Richtext/CustomStyle/translations/highlighted_word.en.yaml"
