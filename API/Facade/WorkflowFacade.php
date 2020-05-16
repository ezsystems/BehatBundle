<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\BehatBundle\API\ContentData\FieldTypeData\PasswordProvider;
use EzSystems\BehatBundle\API\ContentData\RandomDataGenerator;
use EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface;
use EzSystems\EzPlatformWorkflow\Service\WorkflowService;
use EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface;

class WorkflowFacade
{
    /**
     * @var WorkflowRegistryInterface
     */
    private $workflowRegistry;
    /**
     * @var WorkflowServiceInterface
     */
    private $workflowService;

    public function __construct(WorkflowRegistryInterface $workflowRegistry, WorkflowServiceInterface $workflowService)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowService = $workflowService;
    }

    public function transition(Content $content, string $transitionName, string $transitionMessage)
    {
//        $workflows = $this->workflowRegistry->getSupportedWorkflows($content);
//        $workflow = array_shift($workflows);
//        $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($content, $workflow->getName());
        $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($content);
        $this->workflowService->apply($workflowMetadata, $transitionName, $transitionMessage);
    }
}