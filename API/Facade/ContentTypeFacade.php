<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;
use Ibexa\Core\Persistence\Cache\Tag\CacheIdentifierGeneratorInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class ContentTypeFacade
{
    private const CONTENT_TYPE_LIST_BY_GROUP_IDENTIFIER = 'content_type_list_by_group';

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var Symfony\Component\Cache\Adapter\TagAwareAdapterInterface */
    private $cachePool;

    /** @var Ibexa\Core\Persistence\Cache\Tag\CacheIdentifierGeneratorInterface */
    private $cacheIdentifierGenerator;

    private const MAX_LOAD_TRIES = 10;

    public function __construct(
        ContentTypeService $contentTypeService,
        TagAwareAdapterInterface $cachePool,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
        ) {
        $this->contentTypeService = $contentTypeService;
        $this->cachePool = $cachePool;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
    }

    public function createContentType(string $contentTypeName, string $contentTypeIdentifier, string $contentTypeGroupName, string $mainLanguageCode, bool $isContainer, array $fieldDefinitions)
    {
        $contentTypeCreateStruct = $this->contentTypeService->newContentTypeCreateStruct($contentTypeIdentifier);
        $contentTypeCreateStruct->names = [$mainLanguageCode => $contentTypeName];
        $contentTypeCreateStruct->mainLanguageCode = $mainLanguageCode;
        $contentTypeCreateStruct->isContainer = $isContainer;
        $contentTypeCreateStruct->nameSchema = $this->getNameSchema($fieldDefinitions);

        foreach ($fieldDefinitions as $definition) {
            $contentTypeCreateStruct->addFieldDefinition($definition);
        }

        $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupName);

        $contentTypeDraft = $this->contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $this->assertContentTypeExistsInGroup($contentTypeIdentifier, $contentTypeGroup);
    }

    public function contentTypeExists(string $contentTypeIdentifier)
    {
        try {
            $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    public function getFieldTypeIdentifierByName(string $fieldtypeName)
    {
        return EzFieldElement::getFieldInternalNameByName($fieldtypeName);
    }

    private function getNameSchema(array $fieldDefinitions): string
    {
        $fieldDefinitionIdentifiers = array_column($fieldDefinitions, 'identifier');

        return sprintf('<%s>', implode('|', $fieldDefinitionIdentifiers));
    }

    private function assertContentTypeExistsInGroup(string $contentTypeIdentifier, ContentTypeGroup $contentTypeGroup): void
    {
        // Workaround for https://jira.ez.no/browse/EZP-32102: make sure the Content Type is loadable
        $contentTypeIdentifiersInGroup = array_map(function (ContentType $contentType) {
            return $contentType->identifier;
        }, $this->contentTypeService->loadContentTypes($contentTypeGroup));

        $iterationsCount = 0;

        $cacheKey = $this->cacheIdentifierGenerator->generateKey(
            self::CONTENT_TYPE_LIST_BY_GROUP_IDENTIFIER,
            [$contentTypeGroup->id],
            true
        );

        while (!\in_array($contentTypeIdentifier, $contentTypeIdentifiersInGroup)) {
            $this->cachePool->deleteItems([$cacheKey]);

            $contentTypeIdentifiersInGroup = array_map(function (ContentType $contentType) {
                return $contentType->identifier;
            }, $this->contentTypeService->loadContentTypes($contentTypeGroup));

            if ($iterationsCount >= self::MAX_LOAD_TRIES) {
                throw new \Exception('Could not load the Content type after publishing.');
            }

            ++$iterationsCount;
        }
    }
}
