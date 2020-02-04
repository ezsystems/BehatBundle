<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Country\Value;

class CountryDataProvider implements FieldTypeDataProviderInterface
{
    private const COUNTRY_DATA = [
        'BE' => [
            'Name' => 'Belgium',
            'Alpha2' => 'BE',
            'Alpha3' => 'BEL',
            'IDC' => 32,
        ],
        'FR' => [
            'Name' => 'France',
            'Alpha2' => 'FR',
            'Alpha3' => 'FRA',
            'IDC' => 33,
        ],
    ];

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezcountry';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $randomCountry = array_rand(self::COUNTRY_DATA, 1);

        return new Value([$randomCountry => self::COUNTRY_DATA[$randomCountry]]);
    }

    public function parseFromString(string $value)
    {
        return new Value([$value => self::COUNTRY_DATA[$value]]);
    }
}
