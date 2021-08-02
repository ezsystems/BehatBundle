<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Factory\Debug;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Debug\DebuggableElement;
use Ibexa\Behat\Browser\Element\Debug\DebuggableRootElement;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class DebuggableElementFactory implements ElementFactoryInterface
{
    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface */
    private $decoratedElementFactory;

    public function __construct(Session $session, ElementFactoryInterface $decoratedElementFactory)
    {
        $this->session = $session;
        $this->decoratedElementFactory = $decoratedElementFactory;
    }

    public function createElement(LocatorInterface $locator, NodeElement $nodeElement): ElementInterface
    {
        $baseElement = $this->decoratedElementFactory->createElement($locator, $nodeElement);

        return new DebuggableElement($this->session, $baseElement);
    }

    public function createRootElement(Session $session): RootElementInterface
    {
        $baseElement = $this->decoratedElementFactory->createRootElement($session);

        return new DebuggableRootElement($this->session, $baseElement);
    }
}
