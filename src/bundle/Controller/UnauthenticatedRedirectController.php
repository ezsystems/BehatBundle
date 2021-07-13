<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UnauthenticatedRedirectController extends AbstractController
{
    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    public function redirectAction()
    {
        return $this->redirectToRoute('ibexa_testing_current_user_data');
    }
}
