<?php
/**
 * File containing the ObjectManagerInterface for BehatBundle
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Base interface for the object managers
 */
interface ObjectManagerInterface
{
    /**
     * Get instance
     *
     * These objects should be singletons, so Object::instance() should take care of returning
     * the instance and create it when it's not created
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface
     *
     * @return \EzSystems\BehatBundle\ObjectManager\ObjectManagerInterface
     */
    static function instance( KernelInterface $kernel );

    /**
     * Get kenel
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    function getKernel();

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    function getRepository();

    /**
     * Add created test object to list
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object
     */
    function addObjectToList( ValueObject $object );

    /**
     * This method is used to delete each created object from this manager
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    function destroy( ValueObject $object );
}
