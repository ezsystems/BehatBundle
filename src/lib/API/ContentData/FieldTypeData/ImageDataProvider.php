<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Image\Value;

class ImageDataProvider extends AbstractFieldTypeDataProvider
{
    private const IMAGES = [
        'small1.jpg',
        'small2.jpg',
        'medium1.jpg',
        'medium2.jpg',
        'medium3.jpg',
        'medium4.jpg',
        'big1.jpg',
        'big2.jpg',
    ];

    private const IMAGES_PATH = '../../../Data/Images';

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezimage';
    }

    public function generateData(string $language = 'eng-GB')
    {
        $this->setLanguage($language);

        $filename = self::IMAGES[array_rand(self::IMAGES, 1)];
        $filePath = sprintf('%s/%s/%s', __DIR__, self::IMAGES_PATH, $filename);

        return new Value(
            [
                'path' => $filePath,
                'fileSize' => filesize($filePath),
                'fileName' => basename($filePath),
                'alternativeText' => $this->getFaker()->text,
            ]
        );
    }

    public function parseFromString(string $value)
    {
        return new Value(
            [
                'path' => $value,
                'fileSize' => filesize($value),
                'fileName' => basename($value),
                'alternativeText' => $value,
            ]
        );
    }
}
