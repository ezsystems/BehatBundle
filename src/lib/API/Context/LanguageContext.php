<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Context;

use Behat\Behat\Context\Context;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use EzSystems\BehatBundle\API\Facade\LanguageFacade;

class LanguageContext implements Context
{
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
        $this->languageFacade->setUser("admin");
        $this->languageFacade->createLanguageIfNotExists($name, $languageCode);
    }
}