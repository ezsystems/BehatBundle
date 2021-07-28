<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element;

use Behat\Mink\Element\NodeElement;
use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function createValidMinkNodeElement(string $elementText, bool $isVisible = true): NodeElement
    {
        $elementStub = $this->createStub(NodeElement::class);

        $elementStub->method('isValid')->willReturn(true);
        $elementStub->method('getText')->willReturn($elementText);
        $elementStub->method('isVisible')->willReturn($isVisible);

        return $elementStub;
    }

    protected function createElement(string $elementText): ElementInterface
    {
        $element = $this->createStub(ElementInterface::class);
        $element->method('getText')->willReturn($elementText);

        return $element;
    }

    protected function createElementWithChildElement(string $elementText, LocatorInterface $childLocator, string $childElementText): ElementInterface
    {
        $element = $this->createMock(ElementInterface::class);
        $element->method('getText')->willReturn($elementText);
        $element->method('getTimeout')->willReturn(1);
        $element->method('find')->willReturnCallback(function () use ($childLocator, $childElementText) {
            /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface $locator */
            $locator = func_get_args()[0];
            if ($locator == $childLocator) {
                return $this->createElement($childElementText);
            }

            throw new TimeoutException();
        });

        $element->method('findAll')->willReturnCallback(function () use ($childLocator, $childElementText) {
            /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface $locator */
            $locator = func_get_args()[0];
            if ($locator == $childLocator) {
                return $this->createCollection($childLocator, $childElementText);
            }

            return $this->createCollection($childLocator);
        });

        return $element;
    }

    public function createCollection(LocatorInterface $locator, ...$elementTexts): ElementCollection
    {
        $elements = array_map(function (string $elementText) {
            return $this->createElement($elementText);
        }, $elementTexts);

        return new ElementCollection($locator, $elements);
    }
}
