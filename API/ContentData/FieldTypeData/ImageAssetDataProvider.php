<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;
use eZ\Publish\Core\FieldType\ImageAsset\Value;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\BehatBundle\Helper\ArgumentParser;

class ImageAssetDataProvider extends AbstractFieldTypeDataProvider
{
    /**
     * @var AssetMapper
     */
    private $assetMapper;
    /**
     * @var ImageDataProvider
     */
    private $imageDataProvider;

    private $mappings;
    /**
     * @var ArgumentParser
     */
    private $argumentParser;
    /**
     * @var LocationService
     */
    private $locationService;
    /**
     * @var URLAliasService
     */
    private $urlAliasService;

    public function __construct(RandomDataGenerator $randomDataGenerator,
                                AssetMapper $assetMapper,
                                ImageDataProvider $imageDataProvider,
                                ArgumentParser $argumentParser,
                                LocationService $locationService,
                                URLAliasService $urlAliasService,
                                $mappings)
    {
        parent::__construct($randomDataGenerator);
        $this->assetMapper = $assetMapper;
        $this->imageDataProvider = $imageDataProvider;
        $this->argumentParser = $argumentParser;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->mappings = $mappings;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezimageasset';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $this->setLanguage($language);

        $imageAssetContentTypeIdentifier = $this->mappings['content_type_identifier'];
        $imageAssetFieldIdentifier = $this->assetMapper->getContentFieldIdentifier();

        $imageAssetName = $this->getFaker()->realText(80, 1);
        $imageValue = $this->imageDataProvider->generateData($imageAssetContentTypeIdentifier, $imageAssetFieldIdentifier, $language);

        $content = $this->assetMapper->createAsset($imageAssetName, $imageValue, $language);

        $altText = $this->getFaker()->sentence;

        return new Value($content->getVersionInfo()->getContentInfo()->id, $altText);
    }

    public function parseFromString(string $value)
    {
        $locationURL = $this->argumentParser->parseUrl($value);
        $urlAlias = $this->urlAliasService->lookup($locationURL);

        $location = $this->locationService->loadLocation($urlAlias->destination);

        return new Value($location->getContentInfo()->id, $this->getFaker()->realText(100));
    }
}
