<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

use Ibexa\Behat\Browser\Element\ElementInterface;

interface LocatorInterface
{
    public function getIdentifier(): string;

    public function getType(): string;

    public function getSelector(): string;

    public function elementMeetsCriteria(ElementInterface $foundElement): bool;
}
