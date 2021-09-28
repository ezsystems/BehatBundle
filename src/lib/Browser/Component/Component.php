<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Factory\Debug\Highlighting\ElementFactory as HighlightingDebugElementFactory;
use Ibexa\Behat\Browser\Element\Factory\Debug\Interactive\ElementFactory as InteractiveDebugElementFactory;
use Ibexa\Behat\Browser\Element\Factory\ElementFactory;
use Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface;
use Ibexa\Behat\Browser\Element\RootElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorCollection;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

abstract class Component implements ComponentInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorCollection */
    protected $locators;

    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\Factory\ElementFactoryInterface */
    private $elementFactory;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->locators = new LocatorCollection($this->specifyLocators());
        $this->elementFactory = new ElementFactory();
    }

    abstract public function verifyIsLoaded(): void;

    final protected function getHTMLPage(): RootElementInterface
    {
        return $this->elementFactory->createRootElement($this->getSession(), $this->elementFactory);
    }

    public function setElementFactory(ElementFactoryInterface $elementFactory): void
    {
        $this->elementFactory = $elementFactory;
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

    public function enableDebugging(bool $interactive = true, bool $highlighting = true): ElementFactoryInterface
    {
        $oldFactory = $this->elementFactory;

        $factory = new ElementFactory();

        if ($highlighting) {
            $factory = new HighlightingDebugElementFactory($this->session, $factory);
        }

        if ($interactive) {
            $factory = new InteractiveDebugElementFactory($factory);
        }
        $this->setElementFactory($factory);

        return $oldFactory;
    }

    public function disableDebugging(): ElementFactoryInterface
    {
        $oldFactory = $this->elementFactory;
        $this->setElementFactory(new ElementFactory());

        return $oldFactory;
    }
}
