<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Controller;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrentUserDataController extends AbstractController
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\PermissionResolver
     */
    private $permissionResolver;
    /**
     * @var \Ibexa\Contracts\Core\Repository\UserService
     */
    private $userService;

    public function __construct(PermissionResolver $permissionResolver, UserService $userService)
    {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public function showCurrentUserAction(Request $request): Response
    {
        $siteaccess = $request->attributes->get('siteaccess');

        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $userName = $this->userService->loadUser($currentUserId)->getName();

        return $this->render(
            '@eZBehat/tests/login_data.html.twig',
            [
                'username' => $userName,
                'siteaccess' => $siteaccess->name,
            ]
        );
    }
}
