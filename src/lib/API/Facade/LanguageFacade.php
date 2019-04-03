<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;


use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;

class LanguageFacade
{
    private $permissionResolver;
    private $userService;
    private $languageService;

    public function __construct(LanguageService $languageService, UserService $userService, PermissionResolver $permissionResolver)
    {
        $this->languageService = $languageService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    public function setUser(string $username)
    {
        $user = $this->userService->loadUserByLogin($username);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    public function createLanguageIfNotExists(string $name, string $languageCode)
    {
        try {
            $this->languageService->loadLanguage($languageCode);
        }
        catch (NotFoundException $e) {
            $this->createLanguage($name, $languageCode);
        }
    }

    public function createLanguage(string $name, string $languageCode)
    {
        $languageCreateStruct = $this->languageService->newLanguageCreateStruct();
        $languageCreateStruct->languageCode = $languageCode;
        $languageCreateStruct->name = $name;

        $this->languageService->createLanguage($languageCreateStruct);
    }
}