<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use DateTime;
use eZ\Publish\Core\FieldType\Date\Value;

class DateDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezdate';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return new Value($this->getFaker()->dateTimeThisCentury());
    }

    public function parseFromString(string $value)
    {
        return DateTime::createFromFormat('Y-m-d', $value);
    }
}
