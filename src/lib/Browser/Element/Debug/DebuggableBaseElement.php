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

class DebuggableBaseElement implements BaseElementInterface
{
    /** @var \Behat\Mink\Session */
    private $session;

    /** @var \Ibexa\Behat\Browser\Element\BaseElementInterface */
    private $element;

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
        $this->highlight($element);
        $this->addTooltip($element);

        return $element;
    }

    public function findAll(LocatorInterface $locator): ElementCollection
    {
        $elements = $this->element->findAll($locator)->toArray();

        foreach ($elements as $element) {
            $this->highlight($element);
            $this->addTooltip($element);
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

    private function highlight(ElementInterface $element): void
    {
        $this->addClass($element, 'selenium-highlighted');
    }

    private function addTooltip(ElementInterface $element): void
    {
        $highlightingScript = sprintf(
            "document.evaluate(\"%s\", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.setAttribute('data-selenium-locator', 'test')",
                    $element->getXpath(),
                );

        $this->session->executeScript($highlightingScript);

        $this->addClass($element, 'selenium-tooltip');
    }

    private function addClass(ElementInterface $element, string $class): void
    {
        $this->session->executeScript(
            sprintf(
                "document.evaluate(\"%s\", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.classList.add('%s')",
                $element->getXPath(),
                $class
            )
        );
    }
}
