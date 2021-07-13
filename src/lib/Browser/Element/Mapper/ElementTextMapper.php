<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Element\Mapper;

use Ibexa\Behat\Browser\Element\ElementInterface;

class ElementTextMapper implements MapperInterface
{
    public function map(ElementInterface $element): string
    {
        return $element->getText();
    }
}
