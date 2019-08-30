@customStyles
Feature: Example scenarios showing how to set custom styles configuration

  Scenario: Add Highlighted Block custom styles configuration
    Given I "append" configuration to "ezrichtext.custom_styles"
    """
        highlighted_block_test:
            template: '@ezdesign/field_type/ezrichtext/custom_style/highlighted_block.html.twig'
            inline: false
    """
    And I "append" configuration to "admin_group" siteaccess under "fieldtypes.ezrichtext.custom_styles" key
    """
        highlighted_block_test
    """
    And I create a file "app/Resources/views/themes/standard/field_type/ezrichtext/custom_style/highlighted_block.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_block.html.twig"
    And I create a file "app/Resources/views/themes/admin/field_type/ezrichtext/custom_style/highlighted_block.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_block.html.twig"
    And I create a file "app/Resources/translations/custom_styles.en.yaml" with content from "Files/Richtext/CustomStyle/translations/highlighted_block.en.yaml"

  Scenario: Add Highlighted Word custom styles configuration
    Given I "append" configuration to "ezrichtext.custom_styles"
    """
        highlighted_word_test:
            template: '@ezdesign/field_type/ezrichtext/custom_style/highlighted_word.html.twig'
            inline: true
    """
    And I "append" configuration to "admin_group" siteaccess under "fieldtypes.ezrichtext.custom_styles" key
    """
        highlighted_word_test
    """
    And I create a file "app/Resources/views/themes/standard/field_type/ezrichtext/custom_style/highlighted_word.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_word.html.twig"
    And I create a file "app/Resources/views/themes/admin/field_type/ezrichtext/custom_style/highlighted_word.html.twig" with content from "Files/Richtext/CustomStyle/twig/highlighted_word.html.twig"
    And I append to "app/Resources/translations/custom_styles.en.yaml" file "Files/Richtext/CustomStyle/translations/highlighted_word.en.yaml"