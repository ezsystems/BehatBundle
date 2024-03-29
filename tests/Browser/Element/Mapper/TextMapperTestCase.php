<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Browser\Element\Mapper;

use EzSystems\Behat\Test\Browser\Element\BaseTestCase;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use PHPUnit\Framework\Assert;

class TextMapperTestCase extends BaseTestCase
{
    /** @var \Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ElementTextMapper();
    }

    public function testMapsSingleElement(): void
    {
        $element = $this->createElement('Element1');

        Assert::assertEquals('Element1', $this->mapper->map($element));
    }
}
