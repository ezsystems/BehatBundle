<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\API\ContentData\FieldTypeData\PasswordProvider;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\Behat\Browser\Page\FrontendLoginPage;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use PHPUnit\Framework\Assert;

class FrontendContext implements Context
{
    /** @var BrowserContext */
    protected $browserContext;

    /** @var ArgumentParser */
    private $argumentParser;

    public function __construct(ArgumentParser $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /** @BeforeScenario
     * @param BeforeScenarioScope $scope Behat scope
     */
    public function getUtilityContext(BeforeScenarioScope $scope): void
    {
        $this->browserContext = $scope->getEnvironment()->getContext(BrowserContext::class);
    }

    /**
     * @Given I am viewing the pages on siteaccess :siteaccess as :username
     * @Given I am viewing the pages on siteaccess :siteaccess as :username :password
     */
    public function iAmViewingThePagesAsUserOnSiteaccess(string $siteaccess, string $username, string $password = null)
    {
        $loginPage = PageObjectFactory::createPage($this->browserContext, FrontendLoginPage::PAGE_NAME);

        $loginPage->logout($siteaccess);

        if (!$this->shouldPerformLoginAction($username)) {
            return;
        }

        $password = $password ?? PasswordProvider::DEFAUlT_PASSWORD;
        $loginPage->login($siteaccess, $username, $password);
    }

    /**
     * @Given I visit :url on siteaccess :siteaccess
     */
    public function iVisitItemOnSiteaccess(string $url, string $siteaccess): void
    {
        $url = $this->argumentParser->parseUrl($url);
        $this->browserContext->visit(sprintf('/%s%s', $siteaccess, $url));
    }

    /**
     * @Given I see correct preview data for :contentTypeName Content Type
     */
    public function iSeeCorrectPreviewDataFor(string $contentType, TableNode $previewData): void
    {
        $previewType = PageObjectFactory::getPreviewType($contentType);
        $previewPage = PageObjectFactory::createPage($this->browserContext, $previewType);
        Assert::assertEquals($previewData->getHash()[0]['value'], $previewPage->getPageTitle());
    }

    private function shouldPerformLoginAction(string $username)
    {
        return strtolower($username) !== 'anonymous';
    }
}
