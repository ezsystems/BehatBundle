<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Debug;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Element\Condition\ConditionInterface;
use Ibexa\Behat\Browser\Element\ElementCollection;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\LocatorInterface;

class BaseElement implements BaseElementInterface
{
    /** @var \Behat\Mink\Session */
    protected $session;

    /** @var \Ibexa\Behat\Browser\Element\BaseElementInterface */
    protected $element;

    private const TOOLTIP_CLASS = 'ibexa-selenium-tooltip';

    private const HIGHLIGHT_CLASS = 'ibexa-selenium-highlighted';

    private const READ_CLASS = 'ibexa-selenium-read';

    private const COLORS = ['fuchsia', 'green', 'lime', 'maroon', 'navy', 'olive', 'purple', 'red', 'silver', 'teal', 'yellow'];

    public function __construct(Session $session, BaseElementInterface $element)
    {
        $this->session = $session;
        $this->element = $element;
    }

    public function setTimeout(int $timeoutSeconds): BaseElementInterface
    {
        $this->element->setTimeout($timeoutSeconds);

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->element->getTimeout();
    }

    public function find(LocatorInterface $locator): ElementInterface
    {
        $element = $this->element->find($locator);
        $this->highlight($element, $this->getRandomColor());
        $this->addTooltip($element, $locator->getIdentifier());

        return $element;
    }

    public function findAll(LocatorInterface $locator): ElementCollection
    {
        $elements = $this->element->findAll($locator)->toArray();

        $color = $this->getRandomColor();

        foreach ($elements as $element) {
            $this->highlight($element, $color);
            $this->addTooltip($element, $locator->getIdentifier());
        }

        return new ElementCollection($locator, $elements);
    }

    public function waitUntilCondition(ConditionInterface $condition): BaseElementInterface
    {
        return $this->element->waitUntilCondition($condition);
    }

    public function waitUntil(callable $callback, string $errorMessage)
    {
        return $this->element->waitUntil($callback, $errorMessage);
    }

    private function highlight(ElementInterface $element, string $color): void
    {
        $this->setAttribute($element, 'style', sprintf('--ibexa-selenium-color: %s', $color));
        $this->addClass($element, self::HIGHLIGHT_CLASS);
    }

    private function addTooltip(ElementInterface $element, string $value): void
    {
        $text = $this->session->evaluateScript(sprintf('return %s.textContent.trim()', $this->getElementScript($element)));
        // $width = $this->session->evaluateScript(sprintf('return %s.clientWidth;', $this->getElementScript($element)));
        $width = $this->session->evaluateScript(sprintf('return %s.offsetWidth;', $this->getElementScript($element)));

        if ($text === '' || $width < 200) {
            return;
        }

        $this->setAttribute($element, 'data-selenium-locator', $value);
        $this->addClass($element, self::TOOLTIP_CLASS);
    }

    private function addClass(ElementInterface $element, string $class): void
    {
        $this->session->executeScript(
            sprintf(
                "%s.classList.add('%s')",
                $this->getElementScript($element),
                $class
            )
        );
    }

    protected function removeClass(ElementInterface $element, string $class): void
    {
        $this->session->executeScript(
            sprintf(
                "%s.classList.remove('%s')",
                $this->getElementScript($element),
                $class
            )
        );
    }

    protected function removeTooltip(ElementInterface $element): void
    {
        $this->removeClass($element, self::TOOLTIP_CLASS);
    }

    protected function markRead(ElementInterface $element): void
    {
        $this->addClass($element, self::READ_CLASS);
    }

    private function setAttribute(ElementInterface $element, string $attribute, string $value): void
    {
        $this->session->executeScript(
            sprintf(
                "%s.setAttribute('%s', '%s')",
                $this->getElementScript($element),
                $attribute,
                $value
            )
        );
    }

    private function getElementScript(ElementInterface $element): string
    {
        $currentIframe = $this->session->evaluateScript('return self.name');

        if ($currentIframe === '') {
            return sprintf(
                'document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue',
                $element->getXPath()
            );
        }

        // TODO:
        // add support for iframes
    }

    private function getRandomColor(): string
    {
        return self::COLORS[array_rand(self::COLORS)];
    }
}
