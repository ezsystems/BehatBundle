<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Debug;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Debug\Highlighting\Element;
use Ibexa\Behat\Browser\Element\ElementInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class HighlightingElementTest extends TestCase
{
    public function testShouldBeInitializable(): void
    {
        $element = new Element($this->createMock(Session::class), $this->createStub(ElementInterface::class));

        Assert::assertInstanceOf(Element::class, $element);
    }
}

class_alias(HighlightingElementTest::class, 'EzSystems\Behat\Test\Browser\Element\Debug\HighlightingElementTest');
