<?php

namespace EzSystems\BehatBundle\Helpers;

use PHPUnit_Framework_Assert as Assertion;

trait Xpath
{
     /**
     * This is a simple shortcut for
     * $this->getSession()->getPage()->getSelectorsHandler()->xpathLiteral()
     *
     * @param string $text
     */
    public function literal( $text )
    {
        return $this->getSession()->getSelectorsHandler()->xpathLiteral( $text );
    }

    /**
     * Find all elements that match XPath
     *
     * @param string $xpath XPath to find the elements
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that match
     */
    protected function findXpath( $xpath )
    {
        return $this->getSession()->getPage()->findAll( 'xpath', $xpath );
    }

    /**
     * Make XPath for a specific element/object using Behat selectors
     *
     * @param string $element Type of element for the XPath
     * @param string $search String to search
     *
     * @return string XPath for the element/object
     */
    protected function makeElementXpath( $element, $search )
    {
        $selectorsHandler = $this->getSession()->getSelectorsHandler();
        $literal = $selectorsHandler->xpathLiteral( $search );

        return $selectorsHandler
            ->getSelector( 'named' )
            ->translateToXPath( array( $element, $literal ) );
    }

    /**
     * Find page objects/elements
     *
     * @param string $element Object type
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that match
     */
    protected function findObjects( $element, $search, $prefix = null )
    {
        $xpath = $this->mergePrefixToXpath(
            $prefix,
            $this->makeElementXpath( $element, $search )
        );

        return $this->findXpath( $xpath );
    }

    /**
     * Default method to find link elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findLinks( $search, $prefix = null )
    {
        return $this->findObjects( 'link', $search, $prefix );
    }

    /**
     * Default method to find button elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findButtons( $search, $prefix = null )
    {
        return $this->findObjects( 'button', $search, $prefix );
    }

    /**
     * Default method to find fieldset elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findFieldsetss( $search, $prefix = null )
    {
        return $this->findObjects( 'fieldset', $search, $prefix );
    }

    /**
     * Default method to find field elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findFields( $search, $prefix = null )
    {
        return $this->findObjects( 'field', $search, $prefix );
    }

    /**
     * Default method to find content elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findContents( $search, $prefix = null )
    {
        return $this->findObjects( 'content', $search, $prefix );
    }

    /**
     * Default method to find select elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findSelects( $search, $prefix = null )
    {
        return $this->findObjects( 'select', $search, $prefix );
    }

    /**
     * Default method to find checkbox elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findCheckboxs( $search, $prefix = null )
    {
        return $this->findObjects( 'checkbox', $search, $prefix );
    }

    /**
     * Default method to find radio elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findRadios( $search, $prefix = null )
    {
        return $this->findObjects( 'radio', $search, $prefix );
    }

    /**
     * Default method to find file elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findFiles( $search, $prefix = null )
    {
        return $this->findObjects( 'file', $search, $prefix );
    }

    /**
     * Default method to find option elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findOptions( $search, $prefix = null )
    {
        return $this->findObjects( 'option', $search, $prefix );
    }

    /**
     * Default method to find table elements
     *
     * @param string $search Text to search for
     * @param null|string $prefix XPath prefix if needed
     *
     * @return \Behat\Mink\Element\NodeElement[] Array with NodeEelments that matched
     */
    protected function findTables( $search, $prefix = null )
    {
        return $this->findObjects( 'table', $search, $prefix );
    }

    /**
     * Merge/inject prefix into multiple case XPath
     *
     * ex:
     *   $xpath = '//h1 | //h2';
     *   $prefix = '//article';
     *   return "//article/.//h1 | //article/.//h2"
     *
     * @param string $prefix XPath prefix
     * @param string $xpath Complete XPath
     *
     * @return string XPath with prefixes (or original if no prefix passed)
     */
    protected function mergePrefixToXpath( $prefix, $xpath )
    {
        if ( empty( $prefix ) )
        {
            return $xpath;
        }

        if ( $prefix[strlen( $prefix ) - 1] !== '/' )
        {
            $prefix .= '/';
        }

        return $prefix . implode( "| $prefix", explode( '|', $xpath ) );
    }

    /**
     * This method works is a complement to the $mainAttributes var
     *
     * @param  string $block This should be an identifier for the block to use
     *
     * @return null|string XPath for the 
     *
     * @see $this->mainAttributes
     */
    public function makeXpathForBlock( $block = null )
    {
        if ( empty( $block ) )
        {
            return null;
        }

        $parameter = ( isset( $this->mainAttributes[strtolower( $block )] ) ) ?
            $this->mainAttributes[strtolower( $block )] :
            NULL;

        Assertion::assertNotNull( $parameter, "Element {$block} is not defined" );

        $xpath = $this->mainAttributes[strtolower( $block )];
        // check if value is a composed array
        if ( is_array( $xpath ) )
        {
            // if there is an xpath defined look no more!
            if ( isset( $xpath['xpath'] ) )
            {
                return $xpath['xpath'];
            }

            $nuXpath = "";
            // verify if there is a tag
            if ( isset( $xpath['tag'] ) )
            {
                if ( strpos( $xpath['tag'], "/" ) === 0 || strpos( $xpath['tag'], "(" ) === 0 )
                {
                    $nuXpath = $xpath['tag'];
                }
                else
                {
                    $nuXpath = "//" . $xpath['tag'];
                }

                unset( $xpath['tag'] );
            }
            else
            {
                $nuXpath = "//*";
            }

            foreach ( $xpath as $key => $value )
            {
                switch ( $key )
                {
                    case "text":
                        $att = "text()";
                        break;
                    default:
                        $att = "@$key";
                }
                $nuXpath .= "[contains($att, {$this->literal( $value )})]";
            }

            return $nuXpath;
        }

        //  if the string is an Xpath
        if ( strpos( $xpath, "/" ) === 0 || strpos( $xpath, "(" ) === 0 )
        {
            return $xpath;
        }

        // if xpath is an simple tag
        return "//$xpath";
    }
}
