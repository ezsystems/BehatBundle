<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TransitionEvent extends Event
{
    /** @var string */
    public $parentPath;

    /** @var string */
    public $contentTypeIdentifier;

    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    public $content;

    /** @var array */
    private $availableLanguages;

    /** @var array */
    private $potentialReviewers;

    public function __construct(array $potentialReviewers, string $contentTypeIdentifier, string $parentPath, array $availableLanguages)
    {
        $this->parentPath = $parentPath;
        $this->availableLanguages = $availableLanguages;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->potentialReviewers = $potentialReviewers;
    }
}
