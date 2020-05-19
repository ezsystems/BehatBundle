<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

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

    /** @var string */
    public $mainLanguage;

    public function __construct(string $country, string $mainLanguage, array $editors, string $subtreePath, array $languages, array $contentTypes)
    {
        $this->editors = $editors;
        $this->subtreePath = $subtreePath;
        $this->languages = $languages;
        $this->contentTypes = $contentTypes;
        $this->country = $country;
        $this->mainLanguage = $mainLanguage;
    }
}