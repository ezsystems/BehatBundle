<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Media\Value;

class MediaDataProvider implements FieldTypeDataProviderInterface
{
    public const VIDEOS = [
        'video1.mp4',
        'video2.mp4',
    ];

    public const VIDEOS_PATH = '../../../Data/Videos';

    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezmedia' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $filename = self::VIDEOS[array_rand(self::VIDEOS, 1)];
        $filePath = sprintf('%s/%s/%s', __DIR__, self::VIDEOS_PATH, $filename);

        $value = new Value(['inputUri' => $filePath]);
        $value->hasController = true;
        $value->autoplay = true;
        $value->loop = true;

        return $value;
    }

    public function parseFromString(string $value)
    {
        $filePath = sprintf('%s/%s', $this->projectDir, $value);
        $mediaValue = new Value(['inputUri' => $filePath]);
        $mediaValue->hasController = true;
        $mediaValue->autoplay = true;
        $mediaValue->loop = true;

        return $mediaValue;
    }
}
