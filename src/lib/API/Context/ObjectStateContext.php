<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;

class ObjectStateContext implements Context
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    /**
     * @Given Object State Group :objectStateGroupName with identifier :objectStateGroupIdentifier exists
     */
    public function objectStateWithIdentifierExists(string $objectStateGroupName, string $objectStateGroupIdentifier, TableNode $objectStates): void
    {
        $objectStateGroupStruct = $this->objectStateService->newObjectStateGroupCreateStruct($objectStateGroupIdentifier);
        $objectStateGroupStruct->defaultLanguageCode = 'eng-GB';
        $objectStateGroupStruct->names = ['eng-GB' => $objectStateGroupName];

        try {
            $createdGroup = $this->objectStateService->createObjectStateGroup($objectStateGroupStruct);
        } catch (InvalidArgumentException $e) {
            return;
        }

        foreach ($objectStates->getHash() as $objectState) {
            $objectStateStruct = $this->objectStateService->newObjectStateCreateStruct($objectState['objectStates']);
            $objectStateStruct->names = ['eng-GB' => $objectState['objectStates']];
            $objectStateStruct->defaultLanguageCode = 'eng-GB';
            $this->objectStateService->createObjectState($createdGroup, $objectStateStruct);
        }
    }
}

class_alias(ObjectStateContext::class, 'EzSystems\Behat\API\Context\ObjectStateContext');
