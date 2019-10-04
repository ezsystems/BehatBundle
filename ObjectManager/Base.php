<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * @deprecated
 */
abstract class Base implements ObjectManagerInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * Keep the objects created by this class to remove them on a later action.
     *
     * @var array
     */
    private $createdObjects = [];

    /**
     * Disable the possibility to create new instances manually.
     */
    protected function __construct()
    {
        // nothing to do
    }

    /**
     * Set kernel.
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    protected function setContext(KernelAwareContext $context)
    {
        $this->context = $context;
        $this->kernel = $context->getKernel();
    }

    /**
     * Get instance.
     *
     * These objects should be singletons, so Object::instance() should take care of returning
     * the instance and create it when it's not created
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface
     *
     * @return \EzSystems\BehatBundle\ObjectManager\Base
     */
    public static function instance(KernelAwareContext $context)
    {
        static $instance = null;
        if ($instance === null) {
            $class = \get_called_class();
            $instance = new $class();
            $instance->setContext($context);
        }

        return $instance;
    }

    /**
     * Get Context.
     *
     * @return \Behat\Symfony2Extension\Context\KernelAwareContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get Kernel.
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    public function getKernel()
    {
        if (empty($this->kernel)) {
            throw new \Exception('Kernel is not loaded yet.');
        }

        return $this->kernel;
    }

    /**
     * Get repository.
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->repository = $this->getKernel()->getContainer()->get('ezpublish.api.repository');
    }

    /**
     * Add created test object to list.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object
     */
    public function addObjectToList(ValueObject $object)
    {
        $this->createdObjects[] = $object;
    }

    /**
     * Destroy/remove/delete all created objects (from given steps).
     */
    public function clean()
    {
        foreach ($this->createdObjects as $object) {
            $this->destroy($object);
        }

        $this->createdObjects = [];
    }

    /**
     * This method is used to delete each created object from this manager.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object that should be destroyed/removed
     */
    abstract protected function destroy(ValueObject $object);
}
