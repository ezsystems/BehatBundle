<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Debug;

use Exception;
use EzSystems\Behat\Core\Debug\Command\GoBackCommand;
use EzSystems\Behat\Core\Debug\Command\RefreshPageCommand;
use EzSystems\Behat\Core\Debug\Command\ShowHTMLCommand;
use EzSystems\Behat\Core\Debug\Command\ShowURLCommand;
use EzSystems\Behat\Core\Debug\Command\TakeScreenshotCommand;
use EzSystems\Behat\Core\Debug\Shell\Shell;
use Ibexa\Behat\Browser\Component\Component;
use RuntimeException;

trait InteractiveDebuggerTrait
{
    /**
     * @var array Key - name of the variable Value - value of the variable, example: ['myVariable' => 'testValue']
     */
    public function setInteractiveBreakpoint(array $variables = []): void
    {
        $this->startInteractiveSession(null, false, $variables);
    }

    public function startInteractiveSessionOnException(Exception $exception, bool $expectsReturnValue)
    {
        return $this->startInteractiveSession($exception, $expectsReturnValue, []);
    }

    protected function startInteractiveSession(?Exception $exception, bool $isReturnValueExpected, array $variables)
    {
        if ($this->isRunningInteractive()) {
            if ($exception !== null) {
                throw $exception;
            }

            return;
        }

        $component = $this->getCallingComponent();

        // Highlighting mode will be enabled for the interactive session
        $oldFactory = $component->enableDebugging();

        $sh = new Shell();
        $this->addCommands($sh, $component);
        $sh->writeStartupMessages();
        $sh->setBoundObject($component);
        $sh->setScopeVariables($variables);
        $sh->addBaseImports();
        if ($exception !== null) {
            $sh->displayExceptionMessage($exception);
        }

        if ($isReturnValueExpected) {
            $sh->writeMessage('If you want to continue execution you need to create a \'result\' variable and assign to it the Element(s) the test expect.');
        }

        $sh->run();

        // Bring the old debug settings back after the sesion
        $component->setElementFactory($oldFactory);

        if ($isReturnValueExpected) {
            $variables = $sh->getScopeVariables(false);
            if ($variables && array_key_exists('result', $variables)) {
                return $variables['result'];
            }

            throw $exception;
        }

        return null;
    }

    private function getCallingComponent(): Component
    {
        $trace = debug_backtrace();
        foreach ($trace as $traceLine) {
            if (!array_key_exists('function', $traceLine) ||
                    $traceLine['function'] === 'eval' ||
                    !array_key_exists('object', $traceLine)
            ) {
                continue;
            }

            $object = $traceLine['object'];
            if ($object instanceof Component) {
                return $object;
            }
        }

        throw new RuntimeException(
            sprintf("The 'setInteractiveBreakpoint' method can only be called from the '%s' class.", Component::class)
        );
    }

    private function isRunningInteractive(): bool
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);

        foreach ($trace as $traceEntry) {
            if ($traceEntry['function'] === 'eval' && strpos($traceEntry['file'], 'psy/psysh') !== 0) {
                return true;
            }
        }

        return false;
    }

    private function addCommands(Shell $sh, Component $component): void
    {
        // This method should not be public to avoid incorrect usage in Context classess
        $r = new \ReflectionObject($component);
        $getSessionMethod = $r->getMethod('getSession');
        $getSessionMethod->setAccessible(true);
        $session = $getSessionMethod->invoke($component);

        $sh->addCommands([
            new ShowHTMLCommand($session),
            new ShowURLCommand($session),
            new TakeScreenshotCommand($session),
            new RefreshPageCommand($session),
            new GoBackCommand($session),
        ]);
    }
}
