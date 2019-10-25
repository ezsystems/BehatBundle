<?php


namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\DocumentElement as MinkDocomentElement;
use Behat\Mink\Element\DocumentElement as MinkDocumentElement;
use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Test\MinkElementDecorator\NodeElement as TestNodeElement;
use EzSystems\Behat\Test\Session;

class DocumentElement extends MinkDocumentElement
{
    public $timeout;

    /**
     * @var MinkDocumentElement
     */
    protected $decoratedDocumentElement;

    public function __construct(MinkDocomentElement $decoratedDocumentElement)
    {
        parent::__construct($decoratedDocumentElement->getSession());
        $this->decoratedDocumentElement = $decoratedDocumentElement;
        $this->timeout = 10;
    }

    public function waitFor($timeoutSeconds, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = time();
        $end = $start + $timeoutSeconds;
        do {
            try {
                $result = $callback($this);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
            }
            usleep(250 * 1000);
        } while (time() < $end);

        return null;
    }

    public function findAll($selector, $locator): NodeElementCollection
    {
        $elements = $this->waitFor($this->timeout, function () use ($selector, $locator) {
            $elements = $this->decoratedDocumentElement->findAll($selector, $locator);
            foreach ($elements as $element) {
                if (!$element->isValid()) {
                    return false;
                }
            }

            return $elements;
        });

        $wrappedElements = [];

        foreach ($elements as $element) {
            $wrappedElements[] = new TestNodeElement($element);
        }

        return new NodeElementCollection($wrappedElements);
    }

    public function find($selector, $locator): NodeElement
    {
        return $this->waitFor($this->timeout,
                function () use ($selector, $locator) {
                    return $this->decoratedDocumentElement->find($selector, $locator);
                });
//            ?? new NullElement(); cannot use null element if webassert is used
    }

    public function findAllVisible($selector, $locator): NodeElementCollection
    {
        return $this->findAll($selector, $locator)->getVisibleElements();
    }

    public function findVisible($selector, $locator): NodeElement
    {
        return current($this->findAll($selector, $locator)->getVisibleElements());
    }

    // needs to override wait for as it does not take exceptions into account

    public function waitForElementToDisappear($selector, $locator): void
    {
        $currentTimeoutValue = $this->timeout;

        $this->waitFor($this->timeout, function () use ($selector, $locator) {
            $this->timeout = 1;
            return $this->find($selector, $locator)->isVisible() === false;
            });

        $this->timeout = $currentTimeoutValue;
    }

    /**
     * Uploads file from location stored in 'files_path' to the disc on remote browser machine. Mink require uploaded file to be zip archive.
     *
     * @param string $localFileName
     *
     * @return string
     */
    public function uploadFileToRemoteSpace(string $localFileName): string
    {
        // if not instance of selenium2driver return/throw

        if (!preg_match('#[\w\\\/\.]*\.zip$#', $localFileName)) {
            throw new \InvalidArgumentException('Zip archive required to upload to remote browser machine.');
        }

        $localFile = sprintf('%s%s', $this->getMinkParameter('files_path'), $localFileName);


        return $this->getDriver()->getWebDriverSession()->file([
            'file' => base64_encode(file_get_contents($localFile)),
        ]);
    }

    public function moveWithHover(string $startExpression, string $hoverExpression, string $placeholderExpression): void
    {
        // add doc about usage
        $this->loadDraggingLibrary();

        $movingScript = sprintf('dragMock.dragStart(%s).dragOver(%s).delay(100).drop(%s);', $startExpression, $hoverExpression, $placeholderExpression);
        $this->getDriver()->executeScript($movingScript);
    }

    private function isDraggingLibraryLoaded(): bool
    {
        return $this->getDriver()->evaluateScript("typeof(dragMock) !== 'undefined'");
    }

    private function loadDraggingLibrary(): void
    {
        if ($this->isDraggingLibraryLoaded()) {
            return;
        }

        $script = file_get_contents(__DIR__ . '/../lib/drag-mock.js');
        $this->getDriver()->executeScript($script);
        $this->waitFor($this->timeout, function () {
            return $this->isDraggingLibraryLoaded();
        });
    }
}