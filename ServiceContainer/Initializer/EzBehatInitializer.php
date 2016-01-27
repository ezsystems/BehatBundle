<?php

namespace EzSystems\BehatBundle\ServiceContainer\Initializer;

use ReflectionClass;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

/**
 * Behat Context Initializer service
 */
class EzBehatInitializer implements ContextInitializer
{
    /**
     * Service annotation tag
     */
    const SERVICE_DOC_TAG = 'EzBehatService';

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param Kernel $kernel
     */
    public function __construct($kernel)
    {
        $this->container = $kernel->getContainer();
    }

    /**
     * Initializes provided context.
     * Detect all it's methods which use the @EzService
     * annotation and inject the corresponding services.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        foreach ($this->getGetMethodServices($context) as $methodName => $serviceNames) {
            // get services
            $services = [];
            foreach ($serviceNames as $name) {
                $services[] = $this->container->get($name);
            }

            // call method with services as arguments
            call_user_func_array(array($context, $methodName), $services);
        }
    }

    /**
     * Returns an array with all methods and their service requirements,
     * if the methods use the service Annotation
     *
     * @return array array of methods and their service dependencies
     */
    private function getGetMethodServices($object)
    {
        $refClass = new ReflectionClass($object);
        $refMethods = $refClass->getMethods();

        $servicesByMethod = [];

        foreach ($refMethods as $refMethod) {
            preg_match_all('#@(.*?)\n#s', $refMethod->getDocComment(), $matches);
            // parse array from (numeric key => 'annotation <value>') to (annotation => value)
            $methodServices = [];
            foreach ($matches[1] as $annotation) {
                $split = preg_split('/[\s]+/', $annotation, 2);
                $tag = $split[0];
                $value = isset($split[1]) ? $split[1] : '';

                if ($tag == self::SERVICE_DOC_TAG) {
                    $methodServices[] = $value;
                }
            }

            if (!empty($methodServices)) {
                $servicesByMethod[$refMethod->name] = $methodServices;
            }
        }
        return $servicesByMethod;
    }
}
