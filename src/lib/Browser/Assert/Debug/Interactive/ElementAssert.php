<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Assert\Debug\Interactive;

use EzSystems\Behat\Core\Debug\InteractiveDebuggerTrait;
use Ibexa\Behat\Browser\Assert\ElementAssertInterface;
use Ibexa\Behat\Browser\Element\ElementInterface;
use PHPUnit\Framework\ExpectationFailedException;

class ElementAssert implements ElementAssertInterface
{
    use InteractiveDebuggerTrait;

    /** @var \Ibexa\Behat\Browseer\Assert\ElementAssertInterface */
    private $baseElementAssert;

    public function __construct(ElementAssertInterface $baseElementAssert)
    {
        $this->baseElementAssert = $baseElementAssert;
    }

    public function textEquals(string $expectedText): ElementInterface
    {
        try {
            return $this->baseElementAssert->textEquals($expectedText);
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function textContains(string $expectedTextFragment): ElementInterface
    {
        try {
            return $this->baseElementAssert->textContains($expectedTextFragment);
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function isVisible(): ElementInterface
    {
        try {
            return $this->baseElementAssert->isVisible();
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }

    public function hasClass(string $expectedClass): ElementInterface
    {
        try {
            return $this->baseElementAssert->hasClass($expectedClass);
        } catch (ExpectationFailedException $exception) {
            return $this->startInteractiveSessionOnException($exception, true);
        }
    }
}
