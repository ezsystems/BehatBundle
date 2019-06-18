<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Browser\Page\Preview;

use EzSystems\Behat\Browser\Page\Page;

abstract class PreviewPage extends Page
{
    public function setTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    abstract public function getDefaultPreviewData(): ?array;
}
