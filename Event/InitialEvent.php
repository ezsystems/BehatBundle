<?php

namespace EzSystems\BehatBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class InitialEvent extends Event
{
    /** @var array */
    public $editors;

    /** @var string */
    public $subtreePath;

    /** @var array */
    public $languages;

    /** @var array */
    public $contentTypes;

    /** @var string */
    public $country;

    public function __construct(string $country, array $editors, string $subtreePath, array $languages, array $contentTypes)
    {
        $this->editors = $editors;
        $this->subtreePath = $subtreePath;
        $this->languages = $languages;
        $this->contentTypes = $contentTypes;
        $this->country = $country;
    }
}