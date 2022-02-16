<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use DMore\ChromeDriver\ChromeDriver;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use PHPUnit\Framework\Assert;

class BrowserContext extends RawMinkContext
{
    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(ArgumentParser $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I visit :url on siteaccess :siteaccess
     */
    public function iVisitItemOnSiteaccess(string $url, string $siteaccess): void
    {
        $url = $this->argumentParser->parseUrl($url);
        $url = sprintf('/%s%s', $siteaccess, $url);
        $this->getSession()->visit($this->locatePath($url));
    }

    /**
     * @Given response headers contain
     */
    public function responseHeadersContain(TableNode $expectedHeadersData): void
    {
        $responseHeaders = $this->getSession()->getDriver()->getResponseHeaders();

        foreach ($expectedHeadersData->getHash() as $row) {
            Assert::assertEquals($row['Value'], $this->getHeaderValue($responseHeaders, $row['Header']));
        }
    }

    /**
     * @Given response headers match pattern
     */
    public function responseHeadersMatchPattern(TableNode $expectedHeadersData): void
    {
        $responseHeaders = $this->getSession()->getDriver()->getResponseHeaders();

        foreach ($expectedHeadersData->getHash() as $row) {
            $expectedValuePattern = $row['Pattern'];
            $actualValue = $this->getHeaderValue($responseHeaders, $row['Header']);
            Assert::assertEquals(1, preg_match($expectedValuePattern, $actualValue));
        }
    }

    private function getHeaderValue($responseHeaders, $header): string
    {
        if ($this->getSession()->getDriver() instanceof ChromeDriver) {
            return $responseHeaders[$header];
        }

        return $responseHeaders[$header][0];
    }
}
