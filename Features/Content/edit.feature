Feature: Content editing
    As a behat developer
    I need to run content editing scenarios
    In order to test content editing functions

    Scenario: Test content creation and editing
        Given there is an "article" content of type "Article" at "/" with fields:
            """
            title
            =======
            Test Article

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>Content Paragraph.</paragraph>
            </section>
            """
        And content "article" is updated with fields:
            """
            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>New content!</paragraph>
            </section>
            """
        Then content "article" has the fields:
            """
            title
            =======
            Test Article

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>New content!</paragraph>
            </section>
            """
