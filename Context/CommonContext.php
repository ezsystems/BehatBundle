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
use Behat\Gherkin\Node\TableNode;

/**
 * CommonContext contains needed methods and implementations for both
 * API's and browser contexts
 */
trait CommonContext
{
    use ObjectContexts\ContentTypeGroup;

    /**
     * @var array Assossiative array
     */
    private $objectManagers = array();

    /**
     * Associative array with values needed for a test that can't be passed through gherkin
     * for example, objects ID can't be defined through gherkin, so we pass something, and them map
     * it internally
     *
     * @var array Associative array
     */
    private $keyMap = array();

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
     * Store (map) values needed for testing that can't be passed through gherkin
     *
     * @param string $key   (Unique) Identifier key on the array
     * @param mixed $values Any kind of value/object to store
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException if $key is empty
     */
    protected function addValuesToKeyMap( $key, $values )
    {
        if ( empty( $key ) )
        {
            throw new InvalidArgumentException( 'key', "can't be empty" );
        }

        $this->keyMap[$key] = $values;
    }

    /**
     * Fetches values needed for testing stored on mapping
     *
     * @param string $key (Unique) Identifier key on the array
     *
     * @return mixed|null Mapped value or null if not found
     */
    protected function getValuesFromKeyMap( $key )
    {
        if ( empty( $key ) || empty( $this->keyMap[$key] ) )
        {
            return null;
        }

        return $this->keyMap[$key];
    }

    /**
     * Change the mapped values in key map into the intended URL
     *
     * ex:
     *  $keyMap = array(
     *      '{id}'      => 123,
     *      'another'   => 'test',
     *      '{extraId}' => 321
     *  );
     *   URL: 
     *      before: '/some/url/with/another/and/{id}'
     *      after:  '/some/url/with/test/and/123'
     *
     * @param string $url URL to update key mapped values
     *
     * @return string Updated URL
     */
    protected function changeMappedValuesOnUrl( $url )
    {
        $newUrl = "";
        foreach ( explode( '/', $url ) as $chunk )
        {
            $newChunk = $this->getValuesFromKeyMap( $chunk );
            if ( empty( $newChunk ) )
            {
                $newChunk = $chunk;
            }

            $newUrl .= '/' . $newChunk;
        }

        return preg_replace( '/\/\//', '/', $newUrl );
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
}
