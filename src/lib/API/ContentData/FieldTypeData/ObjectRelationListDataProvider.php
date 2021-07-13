<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\RelationList\Value;

class ObjectRelationListDataProvider extends ObjectRelationDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezobjectrelationlist' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->searchFacade->getRandomContentIds(5));
    }

    public function parseFromString(string $value)
    {
        $itemsToAdd = [];
        foreach (explode(',', $value) as $itemToAdd) {
            $itemsToAdd[] = $this->getContentID($itemToAdd);
        }

        return new Value($itemsToAdd);
    }
}
