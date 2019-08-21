<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Api\LimitationParser;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\NewSectionLimitation;

class NewSectionLimitationParser implements LimitationParserInterface
{
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function supports(string $limitationType): bool
    {
        return $limitationType === Limitation::NEWSECTION || strtolower($limitationType) === 'new section';
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
