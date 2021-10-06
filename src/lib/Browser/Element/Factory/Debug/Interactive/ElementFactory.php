<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Factory\Debug\Interactive;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Debug\Interactive\Element;
use Ibexa\Behat\Browser\Element\Debug\Interactive\RootElement;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

final class ElementFactory implements ElementFactoryInterface
{
    /** @var \Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface */
    private $decoratedElementFactory;

    public function __construct(ElementFactoryInterface $decoratedElementFactory)
    {
        $this->decoratedElementFactory = $decoratedElementFactory;
    }

    public function createElement(ElementFactoryInterface $elementFactory, LocatorInterface $locator, NodeElement $nodeElement): ElementInterface
    {
        $baseElement = $this->decoratedElementFactory->createElement($elementFactory, $locator, $nodeElement);

        return new Element($baseElement);
    }

    public function createRootElement(Session $session, ElementFactoryInterface $elementFactory): RootElementInterface
    {
        $baseElement = $this->decoratedElementFactory->createRootElement($session, $elementFactory);

        return new RootElement($baseElement);
    }
}
