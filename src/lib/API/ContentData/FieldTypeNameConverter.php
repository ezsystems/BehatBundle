<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData;

class FieldTypeNameConverter
{
    private static $FIELD_TYPE_MAPPING = [
        'ezauthor' => 'Authors',
        'ezboolean' => 'Checkbox',
        'ezcontentquery' => 'Content query',
        'ezobjectrelation' => 'Content relation (single)',
        'ezobjectrelationlist' => 'Content relations (multiple)',
        'ezcountry' => 'Country',
        'ezdate' => 'Date',
        'ezdatetime' => 'Date and time',
        'ezemail' => 'Email address',
        'ezbinaryfile' => 'File',
        'ezform' => 'Form',
        'ezfloat' => 'Float',
        'ezisbn' => 'ISBN',
        'ezimage' => 'Image',
        'ezimageasset' => 'Image Asset',
        'ezinteger' => 'Integer',
        'ezkeyword' => 'Keywords',
        'ezlandingpage' => 'Landing Page',
        'ezpage' => 'Layout',
        'ezgmaplocation' => 'Map location',
        'ezmatrix' => 'Matrix',
        'ezmedia' => 'Media',
        'ezsrrating' => 'Rating',
        'ezrichtext' => 'Rich text',
        'ezselection' => 'Selection',
        'eztext' => 'Text block',
        'ezstring' => 'Text line',
        'eztime' => 'Time',
        'ezurl' => 'URL',
        'ezuser' => 'User account',
    ];

    public static function getFieldTypeNameByIdentifier(string $fieldTypeIdentifier): string
    {
        return static::$FIELD_TYPE_MAPPING[$fieldTypeIdentifier];
    }

    public static function getFieldTypeIdentifierByName(string $fieldTypeName): string
    {
        return array_search($fieldTypeName, static::$FIELD_TYPE_MAPPING);
    }
}
