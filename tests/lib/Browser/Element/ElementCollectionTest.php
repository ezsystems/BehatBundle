<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Behat\Browser\Element;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class ElementCollectionTest extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Element\ElementCollection */
    private $collection;

    protected function setUp(): void
    {
        $this->collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'Element1',
            'Element2',
            'Element3'
        );
    }

    public function testFirstReturnsFirstElement(): void
    {
        Assert::assertEquals('Element1', $this->collection->first()->getText());
    }

    public function testFirstThrowsExceptionWhenEmpty(): void
    {
        $emptyCollection = $this->createCollection(new CSSLocator('identifier', 'selector'));
        $this->expectException(ElementNotFoundException::class);
        $emptyCollection->first();
    }

    public function testLastReturnsLastElement(): void
    {
        Assert::assertEquals('Element3', $this->collection->last()->getText());
    }

    public function testLastThrowsExceptionWhenEmpty(): void
    {
        $emptyCollection = $this->createCollection(new CSSLocator('identifier', 'selector'));
        $this->expectException(ElementNotFoundException::class);
        $emptyCollection->last();
    }

    public function testCount(): void
    {
        Assert::assertEquals(3, $this->collection->count());
    }

    /**
     * @dataProvider dataProviderTestAny
     */
    public function testAny(ElementCollection $collection, bool $expectedAnyValue): void
    {
        Assert::assertEquals($expectedAnyValue, $collection->any());
    }

    public function dataProviderTestAny(): array
    {
        return [
            [
                $this->createCollection(
                    new CSSLocator('identifier', 'selector'),
                    'Element1',
                    'Element2',
                    'Element3'
                ),
                true,
            ],
            [
                $this->createCollection(
                    new CSSLocator('identifier', 'selector'),
                ),
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestEmpty
     */
    public function testEmpty(ElementCollection $collection, bool $expectedEmptyValue): void
    {
        Assert::assertEquals($expectedEmptyValue, $collection->empty());
    }

    public function dataProviderTestEmpty(): array
    {
        return [
            [
                $this->createCollection(
                    new CSSLocator('identifier', 'selector'),
                    'Element1',
                    'Element2',
                    'Element3'
                ),
                false,
            ],
            [
                $this->createCollection(
                    new CSSLocator('identifier', 'selector'),
                ),
                true,
            ],
        ];
    }

    public function testSingleWithOneElement(): void
    {
        $collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'Element1'
        );

        Assert::assertEquals('Element1', $collection->single()->getText());
    }

    public function testMapBy(): void
    {
        Assert::assertEquals(['Element1', 'Element2', 'Element3'], $this->collection->mapBy(new ElementTextMapper()));
    }

    public function testMap(): void
    {
        Assert::assertEquals(
            ['Element1', 'Element2', 'Element3'],
            $this->collection->map(static function (ElementInterface $element) { return $element->getText(); })
        );
    }

    public function testFilterBy(): void
    {
        $collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'AAA',
            'AAA',
            'ZZZ'
        );

        Assert::assertEquals(
            ['AAA', 'AAA'],
            $collection->filterBy(new ElementTextCriterion('AAA'))->mapBy(new ElementTextMapper())
        );
    }

    public function testFilter(): void
    {
        $collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'AAA',
            'AAA',
            'ZZZ'
        );

        Assert::assertEquals(
            ['AAA', 'AAA'],
            $collection->filter(static function (ElementInterface $element) {
                return $element->getText() === 'AAA';
            })->mapBy(new ElementTextMapper())
        );
    }

    public function testGetBy(): void
    {
        $collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'AAA',
            'BBB',
            'ZZZ'
        );

        Assert::assertEquals(
            'BBB',
            $collection->getByCriterion(new ElementTextCriterion('BBB'))->getText()
        );
    }

    public function testGet(): void
    {
        $collection = $this->createCollection(
            new CSSLocator('identifier', 'selector'),
            'AAA',
            'BBB',
            'ZZZ'
        );

        Assert::assertEquals(
            'BBB',
            $collection->getBy(static function (ElementInterface $element) {
                return $element->getText() === 'BBB';
            })->getText()
        );
    }
}

class_alias(ElementCollectionTest::class, 'EzSystems\Behat\Test\Browser\Element\ElementCollectionTest');
