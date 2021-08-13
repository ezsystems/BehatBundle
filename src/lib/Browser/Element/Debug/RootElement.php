<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\RootElementInterface;

final class RootElement extends BaseElement implements RootElementInterface
{
    public function __construct(Session $session, RootElementInterface $element)
    {
        parent::__construct($session, $element);
    }

    public function dragAndDrop(string $from, string $hover, string $to): void
    {
        $this->element->dragAndDrop($from, $hover, $to);
    }
}
