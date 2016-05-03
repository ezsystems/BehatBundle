<?php

namespace EzSystems\BehatBundle\Context\Resolver;

use Behat\Behat\Context\Argument\ArgumentResolver;
use ReflectionClass;
use Exception;

/**
 * Behat Context Argument Resolver
 *
 * @deprecated deprecated since 6.4, will be removed in 7.0.
 * Use instead EzSystems\PlatformBehatBundle\Context\Argument\AnnotationArgumentResolver
 */
class AnnotationArgumentResolver implements ArgumentResolver
{
    /**
     * Service annotation tag
     */
    const SERVICE_DOC_TAG = 'injectService';

    /**
     * Resolve service arguments for Behat Context constructor thru annotation.
     * Symfony2Extension ArgumentResoler will convert service names to actual instances.
     *
     * @param ReflectionClass $classReflection
     * @param array $arguments
     */
    public function resolveArguments(ReflectionClass $classReflection, array $arguments = [])
    {
        $injArguments = $this->parseAnnotations(
            $this->getMethodAnnotations($classReflection)
        );

        if (!empty($injArguments)) {
            $arguments = [];
            foreach ($injArguments as $name => $service) {
                $arguments[$name] = $service;
            }
        }

        return $arguments;
    }

    /**
     * Returns a array with the method annotations
     *
     * @return array array annotations
     */
    private function getMethodAnnotations($refClass, $method = '__construct')
    {
        $refMethod = $refClass->getMethod($method);
        preg_match_all('#@(.*?)\n#s', $refMethod->getDocComment(), $matches);

        return $matches[1];
    }

    /**
     * Returns an array with the method arguments service requirements,
     * if the methods use the service Annotation
     *
     * @return array array of methods and their service dependencies
     */
    private function parseAnnotations($annotations)
    {
        // parse array from (numeric key => 'annotation <value>') to (annotation => value)
        $methodServices = [];
        foreach ($annotations as $annotation) {
            if (!preg_match('/^(\w+)\s+\$(\w+)\s+([\w\.\@\%]+)/', $annotation, $matches)) {
                continue;
            }

            array_shift($matches);
            $tag = array_shift($matches);
            if ($tag == self::SERVICE_DOC_TAG) {
                list($argument, $service) = $matches;
                $methodServices[$argument] = $service;
            }
        }

        return $methodServices;
    }
}
