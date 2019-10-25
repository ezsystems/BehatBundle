<?php


namespace EzSystems\Behat\Test\PageObject;

use Behat\Mink\Session;

class Element implements ElementInterface
{
    /** @var Session  */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
}