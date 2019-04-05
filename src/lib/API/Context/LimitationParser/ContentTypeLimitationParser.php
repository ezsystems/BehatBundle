<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context\LimitationParser;


use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;

class ContentTypeLimitationParser implements LimitationParserInterface
{
    private $contentTypService;
    private $contentTypeNameIdentifierMap;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypService = $contentTypeService;
        $this->contentTypeNameIdentifierMap = [
            'landing page' => 'landing_page',
            'user group' => 'user_group',
        ];
    }

    public function canWork(string $limitationType): bool
    {
        return $limitationType === Limitation::CONTENTTYPE || in_array($limitationType, ['Content Type', 'Content type']);
    }

    public function parse(string $limitationValue)
    {
        $contentTypeIdentifier = $this->parseCommonContentTypes($limitationValue);
        $contentType = $this->contentTypService->loadContentTypeByIdentifier($contentTypeIdentifier);

        return new ContentTypeLimitation(
            ['limitationValues' => [$contentType->id]]
        );
    }

    private function parseCommonContentTypes(string $contentTypeName): string
    {
        $contentTypeName = strtolower($contentTypeName);

        if (in_array($contentTypeName, $this->contentTypeNameIdentifierMap, true))
        {
            return $this->contentTypeNameIdentifierMap[$contentTypeName];
        }

        return $contentTypeName;
    }
}