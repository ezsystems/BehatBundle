<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Assert\ElementAssert;
use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

final class DebuggableElement implements ElementInterface
{
    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\ElementInterface */
    private $element;

    public function __construct(Session $session, ElementInterface $element)
    {
        $this->session = $session;
        $this->element = $element;
    }

    public function setTimeout(int $timeoutSeconds): BaseElementInterface
    {
        $this->element->setTimeout($timeoutSeconds);

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->element->getTimeout();
    }

    public function find(LocatorInterface $locator): ElementInterface
    {
        $element = $this->element->find($locator);
        $this->highlight($element);

        return $element;
    }

    public function findAll(LocatorInterface $locator): ElementCollection
    {
        $elements = $this->element->findAll($locator)->toArray();

        foreach ($elements as $element) {
            $this->highlight($element);
        }

        return new ElementCollection($locator, $elements);
    }

    public function waitUntilCondition(ConditionInterface $condition): BaseElementInterface
    {
        return $this->element->waitUntilCondition($condition);
    }

    public function waitUntil(callable $callback, string $errorMessage)
    {
        return $this->element->waitUntil($callback, $errorMessage);
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

    private function highlight(ElementInterface $element): void
    {
        $style = 'background: yellow; border: 2px solid red;';

        $highlightingScript = sprintf(
    "document.evaluate(\"%s\", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.setAttribute('style', '%s')",
            $element->getXpath(),
            $style
        );

        $this->session->executeScript($highlightingScript);
    }
}
