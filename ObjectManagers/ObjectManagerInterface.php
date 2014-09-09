<?php

namespace EzSystems\BehatBundle\ObjectManagers;

use eZ\Publish\API\Repository\Values\ValueObject;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @return \EzSystems\BehatBundle\ObjectManagers\ObjectManagerInterface
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
}
