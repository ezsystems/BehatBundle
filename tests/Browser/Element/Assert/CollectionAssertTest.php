<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Assert;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Assert\CollectionAssert;
use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Locator\XPathLocator;
use PHPUnit\Framework\ExpectationFailedException;

class CollectionAssertTest extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Locator\XPathLocator */
    private $locator;

    protected function setUp(): void
    {
        $this->locator = new XPathLocator('locator', '//');
    }

    /**
     * @dataProvider provideForTestAssertionPasses
     */
    public function testAssertionPasses(array $expectedElementTexts, array $actualElementTexts): void
    {
        $collection = $this->createElementCollection($actualElementTexts);
        $collectionAssert = new CollectionAssert($this->locator, $collection);
        $returnedCollection = $collectionAssert->containsElementsWithText($expectedElementTexts);

        $this->assertSame($collection, $returnedCollection);
    }

    /**
     * @dataProvider provideForTestAssertionFails
     */
    public function testAssertionFails(array $expectedElementTexts, array $actualElementTexts): void
    {
        $this->expectException(ExpectationFailedException::class);
        $collectionAssert = new CollectionAssert($this->locator, $this->createElementCollection($actualElementTexts));
        $collectionAssert->containsElementsWithText($expectedElementTexts);
    }

    public static function provideForTestAssertionPasses(): iterable
    {
        return [
            [[], []],
            [[''], ['']],
            [['Test1'], ['Test1']],
            [['Test1', 'Test2'], ['Test1', 'Test2']],
            [['Test1', 'Test2'], ['Test1', 'Test2', 'Test3']],
            [['Test1', 'Test2'], ['Test3', 'Test2', 'Test1']],
            [['Test1', 'Test2'], ['Test3', 'Test2', 'Test1']],
        ];
    }

    public static function provideForTestAssertionFails(): iterable
    {
        return [
            [['Test1'], ['Test2']],
            [['Test1', 'Test2'], ['Test1']],
            [['Test1', 'Test2'], ['Test1', 'Test1']],
        ];
    }

    private function createElementCollection(array $elementTexts): ElementCollection
    {
        return new ElementCollection($this->locator, array_map(function (string $elementText) {
            return $this->createElement($elementText);
        }, $elementTexts));
    }
}
