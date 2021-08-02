<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Factory;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;

interface ElementFactoryInterface
{
    public function createElement(Session $session, LocatorInterface $locator, NodeElement $nodeElement): ElementInterface;

    public function createRootElement(Session $session): RootElementInterface;
}
