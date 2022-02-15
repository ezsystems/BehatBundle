<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Debug\Matcher;

use InvalidArgumentException;
use Psy\TabCompletion\Matcher\AbstractMatcher;
use Psy\TabCompletion\Matcher\ObjectMethodsMatcher;
use ReflectionClass;
use ReflectionMethod;

class ThisObjectMethodsMatcher extends ObjectMethodsMatcher
{
    public function getMatches(array $tokens, array $info = [])
    {
        $input = $this->getInput($tokens);

        $firstToken = \array_pop($tokens);
        if (self::tokenIs($firstToken, self::T_STRING)) {
            // second token is the object operator
            \array_pop($tokens);
        }
        $objectToken = \array_pop($tokens);
        if (!\is_array($objectToken)) {
            return [];
        }
        $objectName = \str_replace('$', '', $objectToken[1]);

        try {
            $object = $this->getVariable($objectName);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        if (!\is_object($object) || $objectName !== 'this') {
            return [];
        }

        $reflectionClass = new ReflectionClass(get_class($object));
        $methods = array_column($reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE), 'name');

        return array_map(static function (string $function) {
            return $function . '()';
        }, \array_filter(
            $methods,
            static function ($methodName) use ($input) {
                return AbstractMatcher::startsWith($input, $methodName);
            }
        ));
    }
}

class_alias(ThisObjectMethodsMatcher::class, 'EzSystems\Behat\Core\Debug\Matcher\ThisObjectMethodsMatcher');
