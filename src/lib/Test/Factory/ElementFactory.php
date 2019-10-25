<?php


namespace EzSystems\Behat\Test\Factory;

use Behat\Mink\Session;
use EzSystems\Behat\Test\PageObject\ElementInterface;

final class ElementFactory implements ElementFactoryInterface
{
    /** @var Session  */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function create(string $elementType): ElementInterface
    {
        // TODO: Implement create() method.
    }

    public function getPreviewType(string $elementType): string
    {
        // TODO: Implement getPreviewType() method.
    }
}