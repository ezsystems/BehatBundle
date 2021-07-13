<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TransitionEvent extends Event
{
    /** @var string */
    public $locationPath;

    /** @var string */
    public $contentTypeIdentifier;

    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    public $content;

    /** @var array */
    public $availableLanguages;

    /** @var array */
    public $editors;

    /** @var string */
    public $author;

    /** @var string */
    public $mainLanguage;

    public function __construct(array $editors, string $contentTypeIdentifier, string $locationPath, array $availableLanguages, string $mainLanguage)
    {
        $this->locationPath = $locationPath;
        $this->availableLanguages = $availableLanguages;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->editors = $editors;
        $this->mainLanguage = $mainLanguage;
    }
}
