<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\QueryType;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\QueryType\QueryType;

class FoldersUnderMediaQueryType implements QueryType
{
    public function getQuery(array $parameters = [])
    {
        return new Query([
            'filter' => new Query\Criterion\LogicalAnd([
                new Criterion\ContentTypeIdentifier($parameters['contentTypeId']),
                new Criterion\ParentLocationId($parameters['locationId']),
            ]),
            'sortClauses' => [new Query\SortClause\DatePublished()],
        ]);
    }

    public function getSupportedParameters()
    {
        return ['contentTypeId', 'locationId'];
    }

    public static function getName()
    {
        return 'Folders under media';
    }
}
