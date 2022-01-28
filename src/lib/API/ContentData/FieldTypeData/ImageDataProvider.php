<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use Ibexa\Core\FieldType\Image\Value;

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

    private $projectDir;

    public function __construct(RandomDataGenerator $randomDataGenerator, $projectDir)
    {
        parent::__construct($randomDataGenerator);
        $this->projectDir = $projectDir;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezimage' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
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
        $filePath = sprintf('%s/%s', $this->projectDir, $value);

        return new Value(
            [
                'path' => $filePath,
                'fileSize' => filesize($value),
                'fileName' => basename($value),
                'alternativeText' => $this->getFaker()->sentence,
            ]
        );
    }
}
