Feature: Content Creation
    As a behat developer
    I need to run content creation scenarios
    In order to test content creation functions

    Scenario: Test content creation at location
        Given a content with location "/TestFolder" doesnt exist
        And there is a "folder" content of type "Folder" at "/" with fields:
            """
            name
            =======
            TestFolder
            """
        Then a content "folder" exists at location "/"
        And content "folder" exists with location "/TestFolder"


    Scenario: Test content creation under parent
        Given there is a "folder" content of type "Folder" at "/" with fields:
            """
            name
            =======
            TestFolder
            """
        And there is an "article" content of type "Article" under "folder" with fields:
            """
            title
            =======
            Test Article

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>This is a paragraph.</paragraph>
            </section>
            """
        Then a content "article" exists under "folder"
        And content "article" is of content type "Article"

    Scenario: Test created content has correct fields
        Given there is an "article" content of type "Article" at location "/" with fields:
            """
            title
            =======
            Test Article

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>This is a paragraph.</paragraph>
            </section>
            """
        Then content "article" is of type "Article"
        And content "article" has the fields:
            """
            title
            =======
            Test Article

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>This is a paragraph.</paragraph>
            </section>
            """

    Scenario: Test existing content with the same fields is found.
        Given there is an "article1" content of type "Article" at location "/" with fields:
            """
            title
            =======
            TestArticle

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>This is a paragraph.</paragraph>
            </section>
            """
        And there is an "article2" content of type "Article" at location "/" with fields:
            """
            title
            =======
            TestArticle

            intro
            ========
            <?xml version="1.0" encoding="utf-8"?>
            <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/">
                <paragraph>This is a paragraph.</paragraph>
            </section>
            """
        Then content "article1" is the same as "article2"
        And content "article1" exists with location "/TestArticle"
        And content "article2" exists with location "/TestArticle"
