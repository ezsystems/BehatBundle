<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use eZ\Publish\API\Repository\Values\User\Limitation;

interface LimitationParserInterface
{
    public const SERVICE_TAG = 'ezplatform.behat.limitation_parser';

    public function supports(string $limitationType): bool;

    public function parse(string $limitationValues): Limitation;
}
