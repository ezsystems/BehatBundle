<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData\FieldTypeData;

use Ibexa\Core\FieldType\BinaryFile\Value;

class BinaryFileDataProvider implements FieldTypeDataProviderInterface
{
    private const FILES = [
        'file1.txt',
        'file2.txt',
    ];
    private const FILES_PATH = '../../../Data/Files';

    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezbinaryfile' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $filename = self::FILES[array_rand(self::FILES, 1)];
        $filePath = sprintf('%s/%s/%s', __DIR__, self::FILES_PATH, $filename);

        return new Value(['inputUri' => $filePath]);
    }

    public function parseFromString(string $value)
    {
        $filePath = sprintf('%s/%s', $this->projectDir, $value);

        return new Value(['inputUri' => $filePath]);
    }
}

class_alias(BinaryFileDataProvider::class, 'EzSystems\Behat\API\ContentData\FieldTypeData\BinaryFileDataProvider');
