<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Debug\Matcher;

use Psy\TabCompletion\Matcher\AbstractMatcher;
use Psy\TabCompletion\Matcher\ObjectMethodsMatcher;

class ObjectFunctionCallChainMatcher extends ObjectMethodsMatcher
{
    public function getMatches(array $tokens, array $info = [])
    {
        $input = $this->getInput($tokens);

        // 1) Split tokens into groups separated by -> - {group1}->{group2}->{group3}
        $groups = [];
        $currentGroup = [];

        foreach ($tokens as $token) {
            if (self::tokenIs($token, self::T_OBJECT_OPERATOR)) {
                $groups[] = $currentGroup;
                $currentGroup = [];
            } else {
                $currentGroup[] = $token;
            }
        }

        // 2) Find an object call and start building a function call chain from that point.
        // The chain is only valid if it lasts till the last token group
        $functionChainStarted = false;
        $chain = [];
        foreach ($groups as $group) {
            if ($functionChainStarted && $this->isFunctionCall($group)) {
                $chain[] = $this->getFunctionName($group);
                continue;
            }

            if (!$this->isFunctionCall($group)) {
                // reset the chain if current group is not a function call
                $chain = [];
                $functionChainStarted = false;
            }

            if ($this->isObjectCall($group)) {
                // start a new chain
                $functionChainStarted = true;
                $chain[] = $this->getObjectClass($group);
                continue;
            }
        }

        if (count($chain) < 2) {
            // chain is too short, other matcher will match it
            return [];
        }

        // 3) Find the return type for the function calls in the chain
        $object = array_shift($chain);
        foreach ($chain as $link) {
            $reflection = new \ReflectionClass($object);
            try {
                $object = $reflection->getMethod($link)->getReturnType()->getName();
            } catch (\ReflectionException $exception) {
                return [];
            }
        }

        // 4) Provide autocomplete for the last return type found in the chain
        return array_map(static function (string $function) {
            return $function . '()';
        }, \array_filter(
            \get_class_methods($object),
            static function ($var) use ($input) {
                return AbstractMatcher::startsWith($input, $var) &&
                    // also check that we do not suggest invoking a super method(__construct, __wakeup, …)
                    !AbstractMatcher::startsWith('__', $var);
            }
        ));
    }

    protected function getTokenName($token)
    {
        if (!is_array($token)) {
            return $token;
        }

        return \token_name($token[0]);
    }

    protected function isFunctionCall($tokenGroup): bool
    {
        return count($tokenGroup) >= 3 && self::tokenIs($tokenGroup[0], self::T_STRING)
                && $tokenGroup[1] === '('
                && end($tokenGroup) === ')';
    }

    protected function getFunctionName($tokenGroup): string
    {
        return $tokenGroup[0][1];
    }

    protected function isObjectCall($tokenGroup): bool
    {
        return self::tokenIs(end($tokenGroup), self::T_VARIABLE);
    }

    protected function getObjectClass($tokenGroup): string
    {
        $objectName = \str_replace('$', '', end($tokenGroup)[1]);
        $object = $this->getVariable($objectName);

        return get_class($object);
    }
}
