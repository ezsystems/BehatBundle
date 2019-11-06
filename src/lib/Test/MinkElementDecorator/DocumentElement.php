<?php


namespace EzSystems\Behat\Test\MinkElementDecorator;

use Behat\Mink\Element\DocumentElement as MinkDocumentElement;

class DocumentElement extends MinkDocumentElement implements ExtendedElementInterface
{
    // zamiast wstrzykiwania ExtendedElementActions mozna pomyslec o Traicie, ale no nie wiem : D

    public function getSession()
    {
        return $this->extendedActions->getSession();
    }

    /** @var ExtendedElementActions  */
    private $extendedActions;

    public function __construct(ExtendedElementActions $extendedActions)
    {
        $this->extendedActions = $extendedActions;
    }

    public function withTimeout(int $timeoutSeconds): DocumentElement
    {
        $this->extendedActions->setTimeout($timeoutSeconds);
        return $this;
    }

    public function find($selector, $locator): ExtendedElementInterface
    {
        return $this->extendedActions->find($selector, $locator);
    }

    public function findAll($selector, $locator): NodeElementCollection
    {
        return $this->extendedActions->findAll($selector, $locator);
    }

    public function findVisible(string $selector, string $locator): ExtendedElementInterface
    {
        return $this->extendedActions->findVisible($selector, $locator);
    }

    public function findAllVisible(string $selector, string $locator): NodeElementCollection
    {
        return $this->extendedActions->findAllVisible($selector, $locator);
    }

    public function waitForElementToDisappear($selector, $locator): void
    {
        $this->extendedActions->waitForElementToDisappear($selector, $locator);
    }

    /**
     * Uploads file from location stored in 'files_path' to the disc on remote browser machine. Mink require uploaded file to be zip archive.
     *
     * @param string $localFileName
     *
     * @return string
     */
    public function uploadFile(string $localFileName): string
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