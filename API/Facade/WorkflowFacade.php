<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface;

class WorkflowFacade
{
    /**
     * @var WorkflowServiceInterface
     */
    private $workflowService;

    public function __construct(WorkflowServiceInterface $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function transition(Content $content, string $transitionName, string $transitionMessage)
    {
        $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($content);
        $this->workflowService->apply($workflowMetadata, $transitionName, $transitionMessage);
    }
}
