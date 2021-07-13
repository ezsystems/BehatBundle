<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\Locator;

class LocatorCollection
{
    /** @var \Ibexa\Behat\Browser\Locator\LocatorInterface[] */
    private $locators;

    public function __construct(array $locators)
    {
        $this->locators = [];

        foreach ($locators as $locator) {
            if (!($locator instanceof LocatorInterface)) {
                throw new \InvalidArgumentException('LocatorCollection accepts only an array of LocatorInterface objects!');
            }

            if ($this->has($locator->getIdentifier())) {
                throw new \InvalidArgumentException(sprintf('Locator with key "%s" already exists! Use the "replace" function to replace an existing locator.', $locator->getIdentifier()));
            }

            $this->locators[$locator->getIdentifier()] = $locator;
        }
    }

    public function get(string $identifier): LocatorInterface
    {
        if ($this->has($identifier)) {
            return $this->locators[$identifier];
        }

        throw new \InvalidArgumentException(
            sprintf(
                "Could not find locator with identifier: '%s'. Available identifiers are: %s",
                $identifier,
                implode(',', array_map(static function (LocatorInterface $locator) {
                    return "'" . $locator->getIdentifier() . "'";
                }, $this->locators))
            )
        );
    }

    public function replace(LocatorInterface $locator): void
    {
        if (!$this->has($locator->getIdentifier())) {
            throw new \InvalidArgumentException(sprintf("LocatorCollection does not contain an element with identifier '%s', replace cannot be executed.", $locator->getIdentifier()));
        }

        $this->locators[$locator->getIdentifier()] = $locator;
    }

    protected function has(string $identifier): bool
    {
        return array_key_exists($identifier, $this->locators);
    }
}
