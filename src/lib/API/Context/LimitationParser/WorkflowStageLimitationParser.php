<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformWorkflow\Value\Limitation\WorkflowStageLimitation;

class WorkflowStageLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return \in_array(strtolower($limitationType), ['workflowstage', 'workflow stage']);
    }

    public function parse(string $limitationValues): Limitation
    {
        $limitations = explode(',', $limitationValues);

        return new WorkflowStageLimitation(
            ['limitationValues' => $limitations]
        );
    }
}
