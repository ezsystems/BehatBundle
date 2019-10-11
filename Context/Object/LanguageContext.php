<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use EzSystems\BehatBundle\API\Facade\LanguageFacade;

class LanguageContext implements Context
{
    /**
     * @var \EzSystems\BehatBundle\API\Facade\LanguageFacade
     */
    private $languageFacade;

    /**
     * @injectService $languageFacade @EzSystems\BehatBundle\API\Facade\LanguageFacade
     */
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
