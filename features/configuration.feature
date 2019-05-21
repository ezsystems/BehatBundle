Feature: Example scenarios showing how to set configuration

  @admin
  Scenario: Add a language. Create a siteaccess using it and add it to PageBuilder
    Given Language "Polski" with code "pol-PL" exists
    And I add a siteaccess "pol" to "site_group" with settings
     | key       | value         |
     | languages | pol-PL,eng-GB |
    And I append configuration to "admin_group" siteaccess
      | key                          | value  |
      | languages                    | pol-PL |
      | page_builder.siteaccess_list | pol    |

  Scenario: Create specified workflows
    And I set configuration to "admin" siteaccess under "workflows" key
    """
      article_workflow:
          name: 'Article Workflow'
          matchers:
              content_type: article
          stages:
              draft:
                  label: 'Draft'
                  color: '#4a69bd'
              done:
                  color: '#0f0'
                  last_stage: true
          initial_stage: draft
          transitions:
              done:
                  from: draft
                  to: done
                  icon: '/bundles/ezplatformadminui/img/ez-icons.svg#approved'
                  label: 'Back to Done'
              back_to_draft:
                  reverse: done
                  icon: '/bundles/ezplatformadminui/img/ez-icons.svg#rejected'
                  label: 'Back to Draft'
    """
    And I append configuration to "admin" siteaccess under "workflows" key
    """
    folder_workflow:
        name: 'Folder Workflow'
        matchers:
            content_type: folder
        stages:
            draft:
                label: 'Draft'
                color: '#0f0'
            done:
                color: '#4a69bd'
                last_stage: true
        initial_stage: draft
        transitions:
            done:
                from: draft
                to: done
                icon: '/bundles/ezplatformadminui/img/ez-icons.svg#approved'
            back_to_draft:
                reverse: done
                icon: '/bundles/ezplatformadminui/img/ez-icons.svg#rejected'
                label: 'Back to Draft'
    """
