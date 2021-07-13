<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\MapLocation\Value;

class MapDataProvider implements FieldTypeDataProviderInterface
{
    private const LOCATION_DATA = [
        'Katowice' => [
            'latitude' => 50.26045,
            'longitude' => 19.01125,
            'address' => 'Gliwicka 6, Katowice, Poland',
        ],
        'Skien' => [
            'latitude' => 59.19930,
            'longitude' => 9.61360,
            'address' => 'Hollenderigata 3, Skien, Norway',
        ],
        'Brooklyn' => [
            'latitude' => 42.10945,
            'longitude' => -84.24696,
            'address' => '215 Water Street, Brooklyn NY, USA',
        ],
        'Tokio' => [
            'latitude' => 35.67048,
            'longitude' => 139.74931,
            'address' => 'Toranomon, Minato-ku, Tokio, Japan',
        ],
    ];

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezgmaplocation' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value(self::LOCATION_DATA[array_rand(self::LOCATION_DATA, 1)]);
    }

    public function parseFromString(string $value)
    {
        return new Value(self::LOCATION_DATA[$value]);
    }
}
