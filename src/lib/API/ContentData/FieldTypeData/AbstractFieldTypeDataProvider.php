<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use Faker\Generator;

abstract class AbstractFieldTypeDataProvider implements FieldTypeDataProviderInterface
{
    private $randomDataGenerator;

    public function __construct(RandomDataGenerator $randomDataGenerator)
    {
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function parseFromString(string $value)
    {
        return $value;
    }

    protected function getFaker(): Generator
    {
        return $this->randomDataGenerator->getFaker();
    }

    public function setLanguage($language)
    {
        $this->randomDataGenerator->setLanguage($language);
    }
}
