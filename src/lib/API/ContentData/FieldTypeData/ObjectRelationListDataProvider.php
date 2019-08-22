<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\RelationList\Value;

class ObjectRelationListDataProvider extends ObjectRelationDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezobjectrelationlist';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return new Value($this->getRandomContentIds(3));
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
