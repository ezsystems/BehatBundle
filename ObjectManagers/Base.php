<?php
/**
 * File containing the Base class for all Object contexts.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManagers;

use EzSystems\BehatBundle\ObjectManagers\ObjectManagerInterface;
use Behat\Behat\Context\BehatContext;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class Base implements ObjectManagerInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * Keep the objects created by this class to remove them on a later action
     *
     * @var array
     */
    private $createdObjects = array();

    /**
     * Disable the possibility to create new instances manually
     */
    protected function __construct()
    {
        // nothing to do
    }

    /**
     * Set kernel
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    protected function setKernel( KernelInterface $kernel )
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
        return $this->repository = $this->getKernel()->getContainer()->get( 'ezpublish.api.repository' );
    }

    /**
     * Add created test object to list
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object
     */
    public function addObjectToList( ValueObject $object )
    {
        $this->createdObjects[] = $object;
    }

    /**
     * Destroy/remove/delete all created objects (from given steps)
     */
    public function clean()
    {
        foreach ( $this->createdObjects as $object )
        {
            $this->destroy( $object );
        }

        $this->createdObjects = array();
    }

    /**
     * This is used by the __destruct() function to delete/remove all the objects
     * that were created for testing
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    abstract protected function destroy( ValueObject $object );
}
