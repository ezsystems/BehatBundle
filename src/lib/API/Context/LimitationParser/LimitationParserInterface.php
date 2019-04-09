<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\BehatBundle\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;

interface LimitationParserInterface
{
    public const SERVICE_TAG = 'behat_limitation_parser';

    public function canWork(string $limitationType): bool;

    public function parse(string $limitationValue): Limitation;
}