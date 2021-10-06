<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Debug;

use Ibexa\Behat\Browser\Element\Debug\Interactive\Element;
use Ibexa\Behat\Browser\Element\ElementInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class InteractiveElementTest extends TestCase
{
    public function testShouldBeInitializable(): void
    {
        $element = new Element($this->createStub(ElementInterface::class));

        Assert::assertInstanceOf(Element::class, $element);
    }
}
