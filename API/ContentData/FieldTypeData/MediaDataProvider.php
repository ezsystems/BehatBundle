<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Media\Value;

class MediaDataProvider implements FieldTypeDataProviderInterface
{
    const VIDEOS = [
        'video1.mp4',
        'video2.mp4',
    ];

    const VIDEOS_PATH = '../../../Data/Videos';

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezmedia';
    }

    public function generateData(string $language = 'eng-GB')
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
        $mediaValue = new Value(['inputUri' => $value]);
        $mediaValue->hasController = true;
        $mediaValue->autoplay = true;
        $mediaValue->loop = true;

        return $mediaValue;
    }
}
