<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context\LimitationParser;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\NewSectionLimitation;

class NewSectionLimitationParser implements LimitationParserInterface
{
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function supports(string $limitationType): bool
    {
        return Limitation::NEWSECTION === $limitationType || 'new section' === strtolower($limitationType);
    }

    public function parse(string $limitationValues): Limitation
    {
        $values = [];

        $givenSectionNames = explode(',', $limitationValues);

        foreach ($this->sectionService->loadSections() as $section) {
            if (\in_array($section->name, $givenSectionNames, true)) {
                $values[] = $section->id;
            }
        }

        return new NewSectionLimitation(
            ['limitationValues' => $values]
        );
    }
}
