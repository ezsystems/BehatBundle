<?php
namespace EzSystems\Behat\Test\Factory;


use EzSystems\Behat\Test\PageObject\ElementInterface;

interface ElementFactoryInterface
{
    public const ELEMENT_FACTORY_TAG = 'ezplatform.behat.element_factory';

    public function create(string $elementType): ElementInterface;

    public function getPreviewType(string $elementType): string;

}