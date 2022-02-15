<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData\FieldTypeData;

use Ibexa\Behat\API\ContentData\RandomDataGenerator;
use Faker\Generator;

abstract class AbstractFieldTypeDataProvider implements FieldTypeDataProviderInterface
{
    protected $randomDataGenerator;

    public function __construct(RandomDataGenerator $randomDataGenerator)
    {
        $this->randomDataGenerator = $randomDataGenerator;
    }

    public function parseFromString(string $value)
    {
        return $value;
    }

    public function setLanguage($language)
    {
        $this->randomDataGenerator->setLanguage($language);
    }

    protected function getFaker(): Generator
    {
        return $this->randomDataGenerator->getFaker();
    }
}

class_alias(AbstractFieldTypeDataProvider::class, 'EzSystems\Behat\API\ContentData\FieldTypeData\AbstractFieldTypeDataProvider');
