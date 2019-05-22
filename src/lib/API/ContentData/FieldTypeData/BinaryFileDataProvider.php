<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\BinaryFile\Value;

class BinaryFileDataProvider implements FieldTypeDataProviderInterface
{
    private const FILES = [
        'file1.txt',
        'file2.txt',
    ];
    private const FILES_PATH = '../../../Data/Files';

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezbinaryfile';
    }

    public function generateData(string $language = 'eng-GB')
    {
        $filename = self::FILES[array_rand(self::FILES, 1)];
        $filePath = sprintf('%s/%s/%s', __DIR__, self::FILES_PATH, $filename);

        return new Value(['inputUri' => $filePath]);
    }
}
