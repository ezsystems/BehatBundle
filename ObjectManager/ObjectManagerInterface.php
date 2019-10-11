<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * * @deprecated in 7.0, will be removed in 8.0.
 *
 * Base interface for the object managers.
 */
interface ObjectManagerInterface
{
    /**
     * Get instance.
     *
     * These objects should be singletons, so Object::instance() should take care of returning
     * the instance and create it when it's not created
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface
     *
     * @return \EzSystems\BehatBundle\ObjectManager\ObjectManagerInterface
     */
    public static function instance(KernelAwareContext $context);

    /**
     * Get kenel.
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    public function getKernel();

    /**
     * Get repository.
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository();

    /**
     * Add created test object to list.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object
     */
    public function addObjectToList(ValueObject $object);

    /**
     * Destroy/remove/delete all created objects (from given steps).
     */
    public function clean();
}
