<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use DateTime;
use eZ\Publish\Core\FieldType\Time\Value;

class TimeDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'eztime';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return Value::fromDateTime($this->getFaker()->dateTimeThisCentury());
    }

    public function parseFromString(string $value)
    {
        return Value::fromDateTime(DateTime::createFromFormat('H:i:s', $value));
    }
}
