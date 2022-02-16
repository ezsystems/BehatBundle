<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData\FieldTypeData;

use Ibexa\Behat\API\ContentData\RandomDataGenerator;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\Core\FieldType\ImageAsset\Value;

class ImageAssetDataProvider extends AbstractFieldTypeDataProvider
{
    /**
     * @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper
     */
    private $assetMapper;

    /**
     * @var ImageDataProvider
     */
    private $imageDataProvider;

    /**
     * @var \Ibexa\Behat\Core\Behat\ArgumentParser
     */
    private $argumentParser;

    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\URLAliasService
     */
    private $urlAliasService;

    /**
     * @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(
        RandomDataGenerator $randomDataGenerator,
        AssetMapper $assetMapper,
        ImageDataProvider $imageDataProvider,
        ArgumentParser $argumentParser,
        LocationService $locationService,
        URLAliasService $urlAliasService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($randomDataGenerator);
        $this->assetMapper = $assetMapper;
        $this->imageDataProvider = $imageDataProvider;
        $this->argumentParser = $argumentParser;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->configResolver = $configResolver;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezimageasset' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $this->setLanguage($language);
        $mappings = $this->configResolver->getParameter('fieldtypes.ezimageasset.mappings');

        $imageAssetContentTypeIdentifier = $mappings['content_type_identifier'];
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

class_alias(ImageAssetDataProvider::class, 'EzSystems\Behat\API\ContentData\FieldTypeData\ImageAssetDataProvider');
