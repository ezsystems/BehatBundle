<?php

namespace EzSystems\Behat\Test\PageObject;


use Behat\Mink\Session;
use Behat\Mink\WebAssert;
use EzSystems\Behat\Test\MinkElementDecorator\DocumentElement;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

abstract class Page implements PageInterface
{
    /** @var string */
    protected $route;

    /**
     * @var \FriendsOfBehat\SymfonyExtension\Mink\MinkParameters
     */
    private $minkParameters;

    /** @var Session */
    private $session;

    /** @var WebAssert */
    private $webAssert;

    public function __construct(Session $session, MinkParameters $minkParameters = null)
    {
        $this->session = $session;
        $this->minkParameters = $minkParameters;
        $this->webAssert = new WebAssert($this->session);
    }

    abstract function verifyIsLoaded(): void;

    abstract public function getName(): string;

    public function open(): void
    {
        $this->tryToOpen();
        $this->verifyIsLoaded();

    }

    public function tryToOpen(): void
    {
        $this->session->visit($this->makeAbsolutePath($this->route));
    }

    protected function getHTMLPage(): DocumentElement
    {
        return $this->session->getPage();
    }

    protected function getWebAssert(): WebAssert
    {
        return $this->webAssert;
    }

    final private function makeAbsolutePath(string $path): string
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/') . '/';
        return 0 !== strpos($path, 'http') ? $baseUrl . ltrim($path, '/') : $path;
    }

    protected function getParameter(string $name): ?string
    {
        return $this->minkParameters[$name] ?? null;
    }
}