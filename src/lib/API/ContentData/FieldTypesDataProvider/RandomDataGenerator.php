<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypesData;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Faker;

class RandomDataGenerator
{
    private $localeConverter;

    protected $faker;

    public function __construct(LocaleConverterInterface $localeConverter)
    {
        $this->localeConverter = $localeConverter;
        $this->faker = Faker\Factory::create($this->localeConverter->convertToPOSIX('eng-GB'));
    }

    public function setLanguage($language)
    {
        $this->faker = Faker\Factory::create($this->localeConverter->convertToPOSIX($language));
    }
}