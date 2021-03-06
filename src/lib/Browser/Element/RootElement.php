<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;
use RuntimeException;

final class RootElement extends BaseElement implements RootElementInterface
{
    /** @var \Behat\Mink\Session */
    private $session;

    public function __construct(Session $session, DocumentElement $baseElement)
    {
        $this->decoratedElement = $baseElement;
        $this->session = $session;
    }

    public function dragAndDrop(string $from, string $hover, string $to): void
    {
        if (!$this->isDraggingLibraryLoaded()) {
            throw new RuntimeException('drag-mock library has to be added to the page in order to use this method. Refer to README in BehatBundle for more information.');
        }

        $movingScript = sprintf('dragMock.dragStart(%s).dragOver(%s).delay(100).drop(%s);', $from, $hover, $to);
        $this->session->getDriver()->executeScript($movingScript);
    }

    private function isDraggingLibraryLoaded(): bool
    {
        return $this->session->getDriver()->evaluateScript("typeof(dragMock) !== 'undefined'");
    }
}
