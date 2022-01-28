<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use DateTime;
use Ibexa\Core\FieldType\DateAndTime\Value;

class DateTimeDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezdatetime' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->getFaker()->dateTimeThisCentury());
    }

    public function parseFromString(string $value)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $value);
    }
}
