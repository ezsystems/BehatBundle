<?php


namespace EzSystems\Behat\Test\MinkElementDecorator;


use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;

class NullElement extends NodeElement
{
    public function __construct()
    {
        parent::__construct('', null);
    }

    public function getXpath()
    {
        return parent::getXpath();
    }

    public function getSession()
    {
        throw new NotImplementedException(sprintf('%s %s', __CLASS__, __METHOD__));
    }

    public function has($selector, $locator)
    {
        return false;
    }

    public function isVisible(): bool
    {
        return false;
    }

    public function isValid()
    {
        return false;
    }

    public function waitFor($timeout, $callback)
    {
        return false;
    }

    public function find($selector, $locator)
    {
        throw new NotImplementedException(sprintf('%s %s', __CLASS__, __METHOD__));
    }

    public function findAll($selector, $locator)
    {
        throw new NotImplementedException(sprintf('%s %s', __CLASS__, __METHOD__));
    }

    public function getText()
    {
        return '';
    }

    public function getHtml()
    {
        return '';
    }
}