<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Ibexa\Behat\API\Facade\LanguageFacade;

class LanguageContext implements Context
{
    /**
     * @var \EzSystems\Behat\API\Facade\LanguageFacade
     */
    private $languageFacade;

    public function __construct(LanguageFacade $languageFacade)
    {
        $this->languageFacade = $languageFacade;
    }

    /**
     * @Given Language :name with code :languageCode exists
     */
    public function createLanguageIfNotExists(string $name, string $languageCode)
    {
        $this->languageFacade->createLanguageIfNotExists($name, $languageCode);
    }
}

class_alias(LanguageContext::class, 'EzSystems\Behat\API\Context\LanguageContext');
