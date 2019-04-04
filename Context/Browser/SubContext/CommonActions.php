<?php
/**
 * File containing the Common Actions for Browser contexts
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Browser\SubContext;

use EzSystems\BehatBundle\Helper\EzAssertion;
use EzSystems\BehatBundle\Helper\Gherkin as GherkinHelper;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Class with the simple actions you can do in a browser
 *
 * @method \Behat\Mink\Session getSession
 */
trait CommonActions
{
    /**
     * @Given I am on :path
     * @When  I go to :path
     *
     * Visits the path ':path', relative to the configured 'base_url' parameter
     */
    public function visit( $path )
    {
        $this->getSession()->visit( $this->locatePath( $path ) );
    }

    /**
     * @Given I am on/at the homepage
     * @Given I am on/at (the) :page page
     * @When I go to the homepage
     * @When  I go to (the) :page page
     *
     * Visits the page identified by ':page', or the homepage.
     * Asserts that http response status code is not >= 400
     */
    public function iAmOnPage( $page = 'home' )
    {
        $this->visit( $this->getPathByPageIdentifier( $page ) );
        $this->checkForExceptions();
    }

    /**
     * Checks the output of the latest session page for Symfony exceptions.
     *
     * If one is found, a failed assertion is executed, with the exception details + formatted stacktrace.
     */
    protected function checkForExceptions()
    {
        $exceptionElements = $this->getXpath()->findXpath("//div[@class='text-exception']/h1");
        $exceptionStackTraceItems = $this->getXpath()->findXpath("//ol[@id='traces-0']/li");
        if (count($exceptionElements) > 0) {
            $exceptionElement = $exceptionElements[0];
            $exceptionLines = [$exceptionElement->getText(), ''];

            foreach ($exceptionStackTraceItems as $stackTraceItem) {
                $html = $stackTraceItem->getHtml();
                $html = substr($html, 0, strpos($html, '<a href', 1));
                $html = htmlspecialchars_decode(strip_tags($html));
                $html = preg_replace('/\s+/', ' ', $html);
                $html = str_replace('  (', '(', $html);
                $html = str_replace(' ->', '->', $html);
                $exceptionLines[] = trim($html);
            }
            $message = 'An exception occured during rendering:' . implode("\n", $exceptionLines);
            Assertion::assertTrue(false, $message);
        }
    }


    /**
     * @Then I should be at/on (the) homepage
     * @Then I should be at/on (the) :page page
     *
     * Asserts that the current page is the one identified by ':page', or the homepage.
     */
    public function iShouldBeOnPage( $pageIdentifier = 'home' )
    {
        $currentUrl = $this->getUrlWithoutQueryString( $this->getSession()->getCurrentUrl() );

        $expectedUrl = $this->locatePath( $this->getPathByPageIdentifier( $pageIdentifier ) );

        Assertion::assertEquals(
            $expectedUrl,
            $currentUrl,
            "Unexpected URL of the current site. Expected: '$expectedUrl'. Actual: '$currentUrl'."
        );
    }
}
