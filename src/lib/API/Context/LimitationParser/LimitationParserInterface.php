<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;

interface LimitationParserInterface
{
    public const SERVICE_TAG = 'ezsystems.behat.limitation_parser';

    public function supports(string $limitationType): bool;

    public function parse(string $limitationValues): Limitation;
}
