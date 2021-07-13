<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\Behat\API\ContentData\RandomDataGenerator;

class BooleanDataProvider extends AbstractFieldTypeDataProvider
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(RandomDataGenerator $randomDataGenerator, ContentTypeService $contentTypeService)
    {
        parent::__construct($randomDataGenerator);
        $this->contentTypeService = $contentTypeService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezboolean' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        if ($contentType->getFieldDefinition($fieldIdentifier)->isRequired) {
            // if the field is required then the value has to be true.
            return true;
        }

        return $this->getFaker()->boolean;
    }

    public function parseFromString(string $value)
    {
        return 'true' === strtolower($value);
    }
}
