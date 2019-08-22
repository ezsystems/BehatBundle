<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\NewObjectStateLimitation;

class NewStateLimitationParser implements LimitationParserInterface
{
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::NEWSTATE || strtolower($limitationType) === 'new state';
    }

    public function parse(string $limitationValues): Limitation
    {
        $objectStates = explode(',', $limitationValues);

        return new NewObjectStateLimitation(
            ['limitationValues' => $this->parseObjectStateValues($objectStates)]
        );
    }

    protected function parseObjectStateValues($objectStates)
    {
        $values = [];

        $groups = $this->objectStateService->loadObjectStateGroups();
        $groupIdentifierId = [];

        foreach ($groups as $group) {
            $groupIdentifierId[$group->identifier] = $group->id;
        }

        foreach ($objectStates as $objectState) {
            [$groupIdentifier, $stateName] = explode(':', $objectState);
            $states = $this->objectStateService->loadObjectStates($this->objectStateService->loadObjectStateGroup($groupIdentifierId[$groupIdentifier]));

            foreach ($states as $state) {
                if ($state->identifier === $stateName) {
                    $values[] = $state->id;
                }
            }
        }

        return $values;
    }
}
