<?php
/**
 * File containing the Gherkin helper for BehatBundle
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Helper;

use Behat\Gherkin\Node\TableNode;

/**
 * Gherkin helper methods to manipulate Node's
 */
class Gherkin
{
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
    static function convertTableToArrayOfData( TableNode $table, array $data = null )
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
