<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData\FieldTypeData;

use DateTime;
use Ibexa\Core\FieldType\Time\Value;

class TimeDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'eztime' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return Value::fromDateTime($this->getFaker()->dateTimeThisCentury());
    }

    public function parseFromString(string $value)
    {
        return Value::fromDateTime(DateTime::createFromFormat('H:i:s', $value));
    }
}

class_alias(TimeDataProvider::class, 'EzSystems\Behat\API\ContentData\FieldTypeData\TimeDataProvider');
