<?php


namespace EzSystems\Behat\Test;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session as MinkSession;
use EzSystems\Behat\Test\MinkElementDecorator\DocumentElement;

class Session extends MinkSession
{
    public function __construct(MinkSession $session)
    {
        parent::__construct($session->getDriver(), $session->getSelectorsHandler());
    }

    public function getPage()
    {
        return new DocumentElement(parent::getPage());
    }
}