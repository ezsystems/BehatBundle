<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;

class ContentDataCreator
{
    private $contentTypeIdentifier;

    private $contentTypeService;

    private $contentService;

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    public function setContentTypeIdentifier(string $contentTypeIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function getRandomContentData($language): ContentCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($this->contentTypeIdentifier);

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language);

        $fieldDefinitions = $contentType->getFieldDefinitions();

        $faker = new Faker\Factory::create();

        foreach ($fieldDefinitions as $field)
        {
            $contentCreateStruct->setField($field->fieldTypeIdentifier, $faker->name);// todo: add strategy per fieldtypeidentifier
        }

    }

    public function getFilledContentDataStruct($contentItemData, $language): ContentCreateStruct
    {

    }
}