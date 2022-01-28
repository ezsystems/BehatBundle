<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use Ibexa\Core\FieldType\Author\Author;
use Ibexa\Core\FieldType\Author\Value;

class AuthorDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezauthor' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB'): Value
    {
        return new Value([$this->getSingleAuthor($language), $this->getSingleAuthor($language)]);
    }

    public function parseFromString(string $value): Value
    {
        [$name, $email] = explode(',', $value);

        $author = new Author();
        $author->name = $name;
        $author->email = $email;

        return new Value([$author]);
    }

    private function getSingleAuthor(string $language = 'eng-GB'): Author
    {
        $this->setLanguage($language);

        $author = new Author();
        $author->name = $this->getFaker()->name;
        $author->email = $this->getFaker()->email;

        return $author;
    }
}
