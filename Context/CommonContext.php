<?php
/**
 * File containing the CommonContext class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use EzSystems\BehatBundle\Helpers;
use EzSystems\BehatBundle\Context\CommonTraits;
use EzSystems\BehatBundle\Context\ObjectContexts;
use EzSystems\BehatBundle\objectManagers\Base as ObjectManager;

/**
 * CommonContext contains needed methods and implementations for both
 * API's and browser contexts
 */
trait CommonContext
{
    use ObjectContexts\ContentTypeGroup;
    use Helpers\KeyMapping;
    use Helpers\ValueObject;

    /**
     * @var array Assossiative array
     */
    private $objectManagers = array();

    /**
     * @BeforeScenario
     */
    public function cleanTestObjects()
    {
        foreach ( $this->objectManagers as $manager )
        {
            $manager->clean();
        }
    }

    /**
     * Get a specific object manager
     *
     * @param string $object Name of the object manager
     *
     * @return \EzSystems\BehatBundle\ObjectManagers\ObjectManagerInterface
     *
     * @throws \Exception $object isn't found or doesn't implement \EzSystems\BehatBundle\ObjectManagers\ObjectManagerInterface
     */
    protected function getObjectManager( $object )
    {
        $namespace = '\\EzSystems\\BehatBundle\\ObjectManagers\\';
        if ( empty( $this->objectManagers[$object] ) )
        {
            $class = $namespace . $object;

            if (
                ! class_exists( $class )
                && is_subclass_of( $class, $namespace . 'ObjectManagerInterface' )
            )
            {
                throw new \Exception( "Class '{$object}' not found or it doesn't implement '{$namespace}ObjectManagerInterface'" );
            }

            $this->objectManagers[$object] = $class::instance( $this->getKernel() );
        }

        return $this->objectManagers[$object];
    }

    /**
     * Get credentials for a specific role
     *
     * @param string $role Role intended for testing
     *
     * @return array Associative with 'login' and 'password'
     */
    protected function getCredentialsFor( $role )
    {
        switch( strtolower( $role ) )
        {
            case 'administrator':
                $user = 'admin';
                $password = 'publish';
                break;

            default:
                throw new PendingException( "Login with '$role' role not implemented yet" );
        }

        return array(
            'login'     => $user,
            'password'  => $password
        );
    }

    /**
     * Verify if the get is for a specific manager
     */
    public function __call( $method, $args )
    {
        preg_match( '/^get(.*)Manager$/', $method, $object );
        if ( !empty( $object[1] ) )
        {
            return $this->getObjectManager( $object[1] );
        }
        else
        {
            throw new \Exception( "Method '$method' doesn't exist in '" . get_class( $this ) . "'" );
        }
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
     *
     * @todo Define better the intended results in all (possible) variations
     */
    protected function convertTableToArrayOfData( TableNode $table, array $data = null )
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
}
