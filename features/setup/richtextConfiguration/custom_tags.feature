@customTags
Feature: Example scenarios showing how to set custom tags configuration

  Scenario: Add YouTube custom tag configuration
    Given I "append" configuration to "ezrichtext.custom_tags"
    """
        ezyoutube_test:
            # The template used for front-end rendering of the custom tag
            template: field_type/ezrichtext/custom_tag/ezyoutube.html.twig
            # An icon for the custom tag as displayed in the Online Editor's toolbar.
            icon: '/bundles/ezplatformrichtext/assets/images/custom_tags/admin/icons/video.svg#video'
            attributes:
                title:
                    type: string
                    required: true
                    default_value: ''
                video_url:
                    type: string
                    required: true
                width:
                    type: number
                    required: true
                    default_value: 640
                height:
                    type: number
                    required: true
                    default_value: 360
                autoplay:
                    type: boolean
                    default_value: false
                align:
                    type: choice
                    required: false
                    default_value: left
                    choices: [left, center, right]
    """
    And I "append" configuration to "default" siteaccess under "fieldtypes.ezrichtext.custom_tags" key
    """
        ezyoutube_test
    """
    And I create a file "templates/field_type/ezrichtext/custom_tag/ezyoutube.html.twig" with content from "Files/Richtext/CustomTag/twig/ezyoutube.html.twig"
    And I create a file "translations/custom_tags.en.yaml" with content from "Files/Richtext/CustomTag/translations/ezyoutube.en.yaml"

  Scenario: Add Twitter custom tag configuration
    Given I "append" configuration to "ezrichtext.custom_tags"
    """
        eztwitter_test:
            template: field_type/ezrichtext/custom_tag/eztwitter.html.twig
            icon: '/bundles/ezplatformrichtext/assets/images/custom_tags/admin/icons/twitter.svg#twitter'
            attributes:
                tweet_url:
                    type: 'string'
                    required: true
                # see https://dev.twitter.com/web/embedded-tweets/parameters
                cards:
                    type: 'choice'
                    required: false
                    default_value: ''
                    choices: ['', 'hidden']
                conversation:
                    type: 'choice'
                    default_value: ''
                    choices: ['', 'none']
                theme:
                    type: 'choice'
                    default_value: 'light'
                    required: true
                    choices: ['light', 'dark']
                link_color:
                    type: 'string'
                    default_value: ''
                width:
                    type: 'number'
                    default_value: 500
                lang:
                    type: 'string'
                    default_value: 'en'
                dnt:
                    type: 'boolean'
                    default_value: true
    """
    And I "append" configuration to "default" siteaccess under "fieldtypes.ezrichtext.custom_tags" key
    """
        eztwitter_test
    """
    And I create a file "templates/field_type/ezrichtext/custom_tag/eztwitter.html.twig" with content from "Files/Richtext/CustomTag/twig/eztwitter.html.twig"
    And I append to "translations/custom_tags.en.yaml" file "Files/Richtext/CustomTag/translations/eztwitter.en.yaml"

  Scenario: Add Facebook custom tag configuration
    Given I "append" configuration to "ezrichtext.custom_tags"
    """
        ezfacebook_test:
            template: field_type/ezrichtext/custom_tag/ezfacebook.html.twig
            icon: '/bundles/ezplatformrichtext/assets/images/custom_tags/admin/icons/facebook.svg#facebook'
            attributes:
                post_url:
                    type: 'string'
                    required: true
                width:
                    type: 'number'
    """
    And I "append" configuration to "default" siteaccess under "fieldtypes.ezrichtext.custom_tags" key
    """
        ezfacebook_test
    """
    And I create a file "templates/field_type/ezrichtext/custom_tag/ezfacebook.html.twig" with content from "Files/Richtext/CustomTag/twig/ezfacebook.html.twig"
    And I append to "translations/custom_tags.en.yaml" file "Files/Richtext/CustomTag/translations/ezfacebook.en.yaml"