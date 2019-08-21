<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData;

use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Faker;
use Faker\Generator;

class RandomDataGenerator
{
    protected const DEFAULT_LANGUAGE = 'eng-GB';

    private $localeConverter;

    private static $faker;

    private $currentLanguage;

    public function __construct(LocaleConverterInterface $localeConverter)
    {
        $this->localeConverter = $localeConverter;
        $this->currentLanguage = self::DEFAULT_LANGUAGE;
        self::$faker = Faker\Factory::create($this->localeConverter->convertToPOSIX(self::DEFAULT_LANGUAGE));
    }

    public function setLanguage($language)
    {
        if ($language !== $this->currentLanguage) {
            self::$faker = Faker\Factory::create($this->localeConverter->convertToPOSIX($language));
            $this->currentLanguage = $language;
        }
    }

    public function getFaker(): Generator
    {
        // Workaround for Faker memory issues, see: https://github.com/fzaninotto/Faker/issues/1125#issuecomment-268676186
        gc_collect_cycles();

        return self::$faker;
    }
}
