<?php
/**
 * File containing the CommonContext class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\BehatBundle\Context\CommonSubContext;
use EzSystems\BehatBundle\ObjectContext;
use EzSystems\BehatBundle\Helpers\ValueObjectHelper;
use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context contains needed methods and implementations for both
 * API's and browser contexts
 */
class CommonContext extends BehatContext implements KernelAwareInterface
{
    const DEFAULT_SITEACCESS_NAME = 'behat_site';

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    public $kernel;

    /**
     * @var \EzSystems\BehatBundle\Helpers\ValueObjectHelper;
     */
    public $valueObjectHelper;

    /**
     * Associative array with values needed for a test that can't be passed through gherkin
     * for example, objects ID can't be defined through gherkin, so we pass something, and them map
     * it internally
     *
     * @var array Associative array
     */
    protected $map = array();

    /**
     * Add the given and common contexts to the main context
     */
    public function __construct()
    {
        // add Common contexts to sub contexts
        $this->getMainContext()->useContext( 'File', new CommonSubContext\File() );

        // add Given contexts sub contexts
        $this->getMainContext()->useContext( 'ObjectContentTypeGroup', new ObjectContext\ContentTypeGroup() );
        $this->getMainContext()->useContext( 'ObjectUserGroup', new ObjectContext\UserGroup() );

        // add helpers
        $this->valueObjectHelper = new ValueObjectHelper();
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel( KernelInterface $kernel )
    {
        $this->kernel = $kernel;
    }

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->kernel->getContainer()->get( 'ezpublish.api.repository' );
    }

    /**
     * @BeforeScenario
     *
     * @param \Behat\Behat\Event\ScenarioEvent|\Behat\Behat\Event\OutlineExampleEvent $event
     */
    public function prepareFeature( $event )
    {
        // Inject a properly generated siteaccess if the kernel is booted, and thus container is available.
        $this->kernel->getContainer()->set( 'ezpublish.siteaccess', $this->generateSiteAccess() );
    }

    /**
     * Remove the objects created by Given steps
     *
     * @AfterScenario
     *
     * @param $event
     */
    public function cleanGivenObjects( $event )
    {
        $this->getMainContext()->getSubContext( 'ObjectContentTypeGroup' )->clean();
    }

    /**
     * Generates the siteaccess
     *
     * @return \eZ\Publish\Core\MVC\Symfony\SiteAccess
     */
    protected function generateSiteAccess()
    {
        $siteAccessName = getenv( 'EZPUBLISH_SITEACCESS' );
        if ( !$siteAccessName )
        {
            $siteAccessName = static::DEFAULT_SITEACCESS_NAME;
        }

        return new SiteAccess( $siteAccessName, 'cli' );
    }

    /**
     * This function will convert Gherkin tables into structure array of data
     *
     * if Gherkin table look like
     *
     *      | field  | value1         | value2 | ... | valueN |
     *      | field1 | single value 1 |        | ... |        |
     *      | field2 | single value 2 |        | ... |        |
     *      | field3 | multiple       | value  | ... | here   |
     *
     * the returned array should look like:
     *      $data = array(
     *          "field1" => "single value 1",
     *          "field2" => "single value 2",
     *          "field3" => array( "multiple", "value", ... ,"here"),
     *          ...
     *      );
     *
     * or if the Gherkin table values comes from a examples table:
     *      | value    |
     *      | <field1> |
     *      | <field2> |
     *      | ...      |
     *      | <fieldN> |
     *
     *      Examples:
     *          | <field1> | <field2> | ... | <fieldN> |
     *          | value1   | value2   | ... | valueN   |
     *
     * the returned array should look like
     *      $data = array(
     *          "field1" => "value1",
     *          "field2" => "value2",
     *          ...
     *          "fieldN" => "valueN",
     *      );
     *
     * @param \Behat\Gherkin\Node\TableNode $table The Gherkin table to extract the values
     * @param array                         $data  If passed the values are concatenated/updated
     *
     * @return false|array
     */
    public function convertTableToArrayOfData( TableNode $table, array $data = null )
    {
        if( empty( $data ) )
            $data = array();

        // prepare given data
        $i = 0;
        $rows = $table->getRows();
        $headers = array_shift( $rows );   // this is needed to take the first row ( readability only )
        foreach ( $rows as $row )
        {
            $count = count( array_filter( $row ) );
            // check if the field is supposed to be empty
            // or it simply has only 1 element
            if (
                $count == 1
                && count( $row )
                && !method_exists( $table, "getCleanRows" )
            )
            {
                $count = 2;
            }

            $key = $row[0];
            switch( $count ){
            // case 1 is for the cases where there is an Examples table and it
            // gets the values from there, so the field name/id shold be on the
            // examples table (ex: "| <field_name> |")
            case 1:
                $value = $key;
                $aux = $table->getCleanRows();
                $k = ( count( $aux ) === count( array_keys( $table ) ) ) ? $i : $i + 1;

                $key = str_replace( array( '<', '>' ), array( '', '' ), $aux[$k][0] );
                break;

            // case 2 is the most simple case where "| field1 | as value 1 |"
            case 2:
                if ( count( $headers ) === 1 )
                {
                    $value = $row[0];
                }
                else
                {
                    $value = $row[1];
                }
                break;

            // this is for the cases where there are several values for the same
            // field (ex: author) and the gherkin table should look like
            default: $value = array_slice( $row, 1 );
                break;
            }
            $data[$key] = $value;
            $i++;
        }

        // if its empty return false otherwise return the array with data
        return empty( $data ) ? false : $data;
    }

    /**
     * Store (map) values needed for testing that can't be passed through gherkin
     *
     * @param string $key   (Unique) Identifier key on the array
     * @param mixed $values Any kind of value/object to store
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException if $key is empty
     */
    public function addValuesToMap( $key, $values )
    {
        if( empty( $key ) )
        {
            throw new InvalidArgumentException( 'key', "can't be empty" );
        }

        $this->map[$key] = $values;
    }

    /**
     * Fetches values needed for testing stored on mapping
     *
     * @param string $key (Unique) Identifier key on the array
     *
     * @return mixed Mapped value
     */
    public function getValuesFromMap( $key )
    {
        if ( empty( $key ) || empty( $this->map[$key] ) )
        {
            return null;
        }
        return $this->map[$key];
    }
}
