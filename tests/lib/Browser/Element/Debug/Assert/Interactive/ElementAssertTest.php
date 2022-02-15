<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Debug\Assert\Interactive;

use Ibexa\Behat\Browser\Assert\Debug\Interactive\ElementAssert;
use Ibexa\Behat\Browser\Assert\ElementAssertInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ElementAssertTest extends TestCase
{
    public function testShouldBeInitializable(): void
    {
        $collectionAssert = new ElementAssert($this->createMock(ElementAssertInterface::class));

        Assert::assertInstanceOf(ElementAssert::class, $collectionAssert);
    }
}

class_alias(ElementAssertTest::class, 'EzSystems\Behat\Test\Browser\Element\Debug\Assert\Interactive\ElementAssertTest');
