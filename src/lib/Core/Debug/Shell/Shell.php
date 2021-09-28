<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Debug\Shell;

use Exception;
use EzSystems\Behat\Core\Debug\Matcher\ObjectFunctionCallChainMatcher;
use EzSystems\Behat\Core\Debug\Matcher\ThisObjectMethodsMatcher;
use Psy\Shell as BaseShell;
use Psy\TabCompletion\Matcher\AbstractMatcher;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class Shell extends BaseShell
{
    /**
     * @return array
     */
    protected function getDefaultMatchers()
    {
        $matchers = array_filter(parent::getDefaultMatchers(), static function (AbstractMatcher $matcher) {
            // Remove FunctionsMatcher as it spams autocomplete too much
            return get_class($matcher) !== FunctionsMatcher::class;
        });

        $matchers[] = new ThisObjectMethodsMatcher();
        $matchers[] = new ObjectFunctionCallChainMatcher();

        return $matchers;
    }

    public function addBaseImports(): void
    {
        $level = 1;
        while (strpos(dirname(__DIR__, $level), 'vendor') !== false) {
            ++$level;
        }
        $dir = dirname(__DIR__, $level);
        $classess = array_keys(require $dir . '/vendor/composer/autoload_classmap.php');

        $baseImports = array_filter($classess, static function (string $classFcqn) {
            return strpos($classFcqn, 'Ibexa\Behat\Browser\Element') === 0 ||
                strpos($classFcqn, 'Ibexa\Behat\Browser\Locator') === 0;
        });

        foreach ($baseImports as $import) {
            $this->addInput(sprintf('use %s;', $import), true);
        }
    }

    public function writeStartupMessages(): void
    {
        $messages = [
            '🕵️  Welcome to interactive debugging mode.',
            "You can start by running 'trace --num 5' to get an idea which code has been executed so far.",
        ];

        foreach ($messages as $message) {
            $this->writeMessage($message);
        }
    }

    public function writeMessage(string $message): void
    {
        $this->addInput(sprintf('sprintf("%s");', $message), true);
    }

    public function displayExceptionMessage(Exception $e)
    {
        $this->writeMessage('The error message is: ' . $e->getMessage());
    }
}
