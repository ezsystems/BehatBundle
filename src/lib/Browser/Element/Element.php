<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Behat\Mink\Element\NodeElement;
use Ibexa\Behat\Browser\Assert\ElementAssert;
use Ibexa\Behat\Browser\Assert\ElementAssertInterface;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Webdriver\Exception\NoSuchElement;
use WebDriver\Exception\StaleElementReference;

final class Element extends BaseElement implements ElementInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface */
    private $locator;

    public function __construct(ElementFactoryInterface $elementFactory, LocatorInterface $locator, NodeElement $baseElement)
    {
        parent::__construct($elementFactory);
        $this->decoratedElement = $baseElement;
        $this->locator = $locator;
    }

    public function isVisible(): bool
    {
        try {
            return $this->decoratedElement->isVisible();
        } catch (StaleElementReference $e) {
            return false;
        } catch (NoSuchElement $element) {
            return false;
        }
    }

    public function getText(): string
    {
        return $this->decoratedElement->getText();
    }

    public function setValue($value): void
    {
        if ($this->isCheckbox()) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $this->decoratedElement->setValue($value);

            return;
        }

        if ($value && $this->isRadioGroup()) {
            $this->decoratedElement->click();

            return;
        }

        $this->decoratedElement->setValue($value);
    }

    public function clear(): void
    {
        $this->decoratedElement->setValue('');
    }

    public function click(): void
    {
        $this->decoratedElement->click();
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->decoratedElement->hasAttribute($attribute);
    }

    public function getAttribute(string $attribute): string
    {
        return $this->decoratedElement->getAttribute($attribute) ?? '';
    }

    public function hasClass(string $class): bool
    {
        return $this->decoratedElement->hasClass($class);
    }

    public function mouseOver(): void
    {
        $this->decoratedElement->mouseOver();
    }

    public function getValue()
    {
        return $this->decoratedElement->getValue();
    }

    public function attachFile($filePath): void
    {
        $this->decoratedElement->attachFile($filePath);
    }

    public function getOuterHtml(): string
    {
        return $this->decoratedElement->getOuterHtml();
    }

    public function assert(): ElementAssertInterface
    {
        return new ElementAssert($this->locator, $this);
    }

    public function isValid(): bool
    {
        return null !== $this->decoratedElement ? $this->decoratedElement->isValid() : false;
    }

    public function selectOption(string $option): void
    {
        $this->decoratedElement->selectOption($option);
    }

    public function getXPath(): string
    {
        return $this->decoratedElement->getXpath();
    }

    protected function isCheckbox(): bool
    {
        return $this->decoratedElement->hasAttribute('type') && 'checkbox' === $this->decoratedElement->getAttribute('type');
    }

    protected function isRadioGroup(): bool
    {
        return $this->decoratedElement->hasAttribute('type') && 'radio' === $this->decoratedElement->getAttribute('type');
    }
}
