<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Factory\Debug\DebuggableElementFactory;
use Ibexa\Behat\Browser\Element\Factory\ElementFactory;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

abstract class Component implements ComponentInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorCollection */
    protected $locators;

    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\ElementFactoryInterface */
    private $elementFactory;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->locators = new LocatorCollection($this->specifyLocators());
        $this->elementFactory = new DebuggableElementFactory($session, new ElementFactory());
    }

    abstract public function verifyIsLoaded(): void;

    final protected function getHTMLPage(): RootElementInterface
    {
        return $this->elementFactory->createRootElement($this->getSession());
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
