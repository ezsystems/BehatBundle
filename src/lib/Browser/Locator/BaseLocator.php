<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

use Ibexa\Behat\Browser\Element\ElementInterface;

abstract class BaseLocator implements LocatorInterface
{
    protected $selector;

    protected $identifier;

    public function __construct(string $identifier, string $selector)
    {
        $this->identifier = $identifier;
        $this->selector = $selector;
    }

    abstract public function getType(): string;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function elementMeetsCriteria(?ElementInterface $foundElement): bool
    {
        return null !== $foundElement && $foundElement->isValid();
    }
}
