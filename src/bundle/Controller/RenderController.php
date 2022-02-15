<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\MVC\Symfony\View\ContentView;

class RenderController extends Controller
{
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function embedAction(ContentView $view): ContentView
    {
        $destinationContentId = $view->getContent()->getFieldValue('relation')->destinationContentId;
        $content = $this->contentService->loadContent($destinationContentId);
        $view->addParameters(['embeddedItem' => $content]);

        return $view;
    }

    public function longAction(ContentView $view): ContentView
    {
        sleep(5);

        return $view;
    }
}

class_alias(RenderController::class, 'EzSystems\BehatBundle\Controller\RenderController');
