<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\RootElement;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

abstract class Component implements ComponentInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorCollection */
    protected $locators;

    /** @var \Behat\Mink\Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->locators = new LocatorCollection($this->specifyLocators());
    }

    abstract public function verifyIsLoaded(): void;

    final protected function getHTMLPage(): RootElementInterface
    {
        return new RootElement($this->getSession(), $this->getSession()->getPage());
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return \Ibexa\Behat\Browser\Locator\LocatorInterface[]
     */
    abstract protected function specifyLocators(): array;

    final protected function getLocator(string $identifier): LocatorInterface
    {
        return $this->locators->get($identifier);
    }
}
