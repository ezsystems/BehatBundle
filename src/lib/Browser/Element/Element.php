<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Element;

use EzSystems\Behat\Browser\Context\BrowserContext;

/** Abstract for pages elements */
abstract class Element
{
    /** @var int */
    public $defaultTimeout = 5;

    /* @var \EzSystems\Behat\Browser\Context\BrowserContext */
    protected $context;

    protected $fields;

    public function __construct(BrowserContext $context)
    {
        $this->context = $context;
    }

    public function verifyVisibility(): void
    {
    }
}
