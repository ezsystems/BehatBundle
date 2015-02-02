Feature: Content Deletion
    As a behat developer
    I need to run content deletion scenarios
    In order to test content deletion functions

    Scenario: Test content does not exist
        Given a content with location "/Article" does not exist
        Then a content does not exist with location "/Article"

    Scenario: Test content creation and removal
        Given there is an "article" content of type "Article" at "/" with fields:
            """
            title
            =======
            Test Article for removal

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>Content Paragraph.</paragraph>
            </section>
            """
        And content "article" is removed
        Then content "article" does not exist

    Scenario: Test content creation and removal with location
        Given there is an "article" content of type "Article" at "/" with fields:
            """
            title
            =======
            TestArticle

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                    <paragraph>Content Paragraph.</paragraph>
            </section>
            """
        And content "article" exists with location "/TestArticle"
        And content "article" is removed
        Then a content does not exist with location "/TestArticle"

