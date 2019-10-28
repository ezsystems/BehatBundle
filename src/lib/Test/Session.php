<?php


namespace EzSystems\Behat\Test;

use Behat\Mink\Session as MinkSession;
use EzSystems\Behat\Test\MinkElementDecorator\DocumentElement;
use EzSystems\Behat\Test\MinkElementDecorator\ExtendedElementActions;

class Session extends MinkSession
{
    public function __construct(MinkSession $session)
    {
        parent::__construct($session->getDriver(), $session->getSelectorsHandler());
    }

    public function getPage(): DocumentElement
    {
        return new DocumentElement(new ExtendedElementActions(parent::getPage()));
    }
}