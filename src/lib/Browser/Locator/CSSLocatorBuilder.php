<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

class CSSLocatorBuilder
{
    private $result;

    protected function __construct(CSSLocator $base)
    {
        $this->result = $base;
    }

    public static function base(CSSLocator $baseElement): self
    {
        return new CSSLocatorBuilder($baseElement);
    }

    public function build(): CSSLocator
    {
        return $this->result;
    }

    public function withDescendant(CSSLocator $locator): self
    {
        $this->result = self::combine('%s %s', $this->result, $locator);

        return $this;
    }

    public function withParent(CSSLocator $locator): self
    {
        $this->result = self::combine('%2$s %1$s', $this->result, $locator);

        return $this;
    }

    public static function combine(string $format, ...$locators): CSSLocator
    {
        $joinedLocatorIDs = 'combined-' . implode('-', array_map(static function (CSSLocator $locator) {
            return $locator->getIdentifier();
        }, $locators));

        $locatorValues = array_map(static function (CSSLocator $locator) {
            return $locator->getSelector();
        }, $locators);

        $type = get_class($locators[0]);

        return new $type($joinedLocatorIDs, sprintf($format, ...$locatorValues));
    }
}
