<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context\LimitationParser;

class LimitationParsersCollector
{
    private $limitationParsers;

    public function addLimitationParser(LimitationParserInterface $limitationParser): void
    {
        $this->limitationParsers[] = $limitationParser;
    }

    /**
     * @return LimitationParserInterface[]
     */
    public function getLimitationParsers(): array
    {
        return $this->limitationParsers;
    }
}
