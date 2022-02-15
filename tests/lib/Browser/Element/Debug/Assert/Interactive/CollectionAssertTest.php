<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element\Debug\Assert\Interactive;

use Ibexa\Behat\Browser\Assert\CollectionAssertInterface;
use Ibexa\Behat\Browser\Assert\Debug\Interactive\CollectionAssert;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CollectionAssertTest extends TestCase
{
    public function testShouldBeInitializable(): void
    {
        $collectionAssert = new CollectionAssert($this->createMock(CollectionAssertInterface::class));

        Assert::assertInstanceOf(CollectionAssert::class, $collectionAssert);
    }
}

class_alias(CollectionAssertTest::class, 'EzSystems\Behat\Test\Browser\Element\Debug\Assert\Interactive\CollectionAssertTest');
