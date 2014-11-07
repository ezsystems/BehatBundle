<?php
/**
 * File containing the master class for BehatBundle.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context;

use EzSystems\BehatBundle\Helper;
use EzSystems\BehatBundle\Context\Object;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * EzContext has all the needed methods and helpers that are globaly used in contexts
 */
class EzContext implements KernelAwareContext
{
    use Object\ContentTypeGroup;
    use Object\UserGroup;
    use Object\User;

    const DEFAULT_SITEACCESS_NAME = 'behat_site';

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

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
     * Get kenel
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    public function getKernel()
    {
        if ( empty( $this->kernel ) )
        {
            throw new \Exception( 'Kernel is not loaded yet.' );
        }

        return $this->kernel;
    }

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->getKernel()->getContainer()->get( 'ezpublish.api.repository' );
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
     * @BeforeScenario
     *
     * @param \Behat\Behat\Event\ScenarioEvent|\Behat\Behat\Event\OutlineExampleEvent $event
     */
    public function prepareKernel( $event )
    {
        // Inject a properly generated siteaccess if the kernel is booted, and thus container is available.
        $this->getKernel()->getContainer()->set( 'ezpublish.siteaccess', $this->generateSiteAccess() );
    }

    /**
     * @BeforeScenario
     */
    public function clearKeyMap()
    {
        $this->keyMap = array();
    }

    /**
     * @AfterScenario
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
     * @return \EzSystems\BehatBundle\ObjectManager\ObjectManagerInterface
     *
     * @throws \Exception $object isn't found or doesn't implement \EzSystems\BehatBundle\ObjectManager\ObjectManagerInterface
     */
    protected function getObjectManager( $object )
    {
        $namespace = '\\EzSystems\\BehatBundle\\ObjectManager\\';
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

            $this->objectManagers[$object] = $class::instance( $this );
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

        if ( ! empty( $this->keyMap[$key] ) )
        {
            throw new InvalidArgumentException( 'key', 'key exists' );
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
