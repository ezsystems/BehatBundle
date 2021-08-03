<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Assert\ElementAssert;
use Ibexa\Behat\Browser\Element\ElementInterface;

final class Element extends BaseElement implements ElementInterface
{
    public function __construct(Session $session, ElementInterface $element)
    {
        parent::__construct($session, $element);
    }

    public function isVisible(): bool
    {
        return $this->element->isVisible();
    }

    public function getText(): string
    {
        return $this->element->getText();
    }

    public function setValue($value): void
    {
        $this->element->setValue($value);
    }

    public function clear(): void
    {
        $this->element->clear();
    }

    public function click(): void
    {
        usleep(750000);
        $this->removeTooltip($this);
        $this->markClicked($this);
        usleep(250000);
        $this->element->click();
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->element->hasAttribute($attribute);
    }

    public function getAttribute(string $attribute): string
    {
        return $this->element->getAttribute($attribute);
    }

    public function hasClass(string $class): bool
    {
        return $this->element->hasClass($class);
    }

    public function mouseOver(): void
    {
        $this->element->mouseOver();
    }

    public function getValue()
    {
        return $this->element->getValue();
    }

    public function attachFile($filePath): void
    {
        $this->element->attachFile($filePath);
    }

    public function getOuterHtml(): string
    {
        return $this->element->getOuterHtml();
    }

    public function assert(): ElementAssert
    {
        return $this->element->assert();
    }

    public function isValid(): bool
    {
        return $this->element->isValid();
    }

    public function selectOption(string $option): void
    {
        $this->element->selectOption($option);
    }

    public function getXPath(): string
    {
        return $this->element->getXPath();
    }
}
