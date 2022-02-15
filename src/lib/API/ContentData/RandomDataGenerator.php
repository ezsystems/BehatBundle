<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\ContentData;

use DateTime;
use Faker;
use Faker\Generator;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;

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

    public function getRandomDateFromThePast(): DateTime
    {
        return $this->getFaker()->dateTimeThisDecade();
    }

    public function getRandomTextLine(): string
    {
        return $this->getFaker()->text();
    }

    public function getRandomDateInTheFuture(): DateTime
    {
        return $this->getFaker()->dateTimeBetween('now', '+10 years');
    }

    /**
     * @return float Returns a random number between 0 and 1, left-inclusive
     */
    public function getRandomProbability(): float
    {
        return random_int(0, 999) / 1000;
    }
}

class_alias(RandomDataGenerator::class, 'EzSystems\Behat\API\ContentData\RandomDataGenerator');
