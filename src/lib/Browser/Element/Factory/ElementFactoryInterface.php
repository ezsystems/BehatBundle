<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Factory;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

interface ElementFactoryInterface
{
    public function createElement(LocatorInterface $locator, NodeElement $nodeElement): ElementInterface;

    public function createRootElement(Session $session): RootElementInterface;
}
