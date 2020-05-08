<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TransitionEvent extends Event
{
    public const START = 'start';

    public const DRAFT_TO_PUBLISH = 'transition.draft_to_publish';

    public const DRAFT_TO_END = 'transition.draft_to_end';

    public const PUBLISH_TO_END = 'transition.publish_to_end';

    public const PUBLISH_TO_EDIT = 'transition.publish_to_edit';

    public const EDIT_TO_PUBLISH = 'transition.edit_to_publish';

    public const EDIT_TO_END = 'transition.edit_to_end';

    public const EDIT_TO_EDIT = 'transition.edit_to_edit';

    /**
     * @var string
     */
    public $userName;
    /**
     * @var string
     */
    public $parentPath;
    /**
     * @var string
     */
    public $language;
    /**
     * @var string
     */
    public $contentTypeIdentifier;
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    public $content;

    public function __construct(string $userName, string $contentTypeIdentifier, string $parentPath, string $language)
    {
        $this->userName = $userName;
        $this->parentPath = $parentPath;
        $this->language = $language;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }
}
