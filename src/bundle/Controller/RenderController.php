<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\API\Repository\ContentService;

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
