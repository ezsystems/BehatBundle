<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;

class AuthorDataProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezauthor';
    }

    public function generateData(string $language = 'eng-GB'): Value
    {
        return new Value([$this->getSingleAuthor($language), $this->getSingleAuthor($language)]);
    }

    private function getSingleAuthor(string $language = 'eng-GB'): Author
    {
        $this->setLanguage($language);

        $author = new Author();
        $author->name = $this->getFaker()->name;
        $author->email = $this->getFaker()->email;

        return $author;
    }

    public function parseFromString(string $value): Value
    {
        [$name, $email] = explode(',', $value);

        $author = new Author();
        $author->name = $name;
        $author->email = $email;

        return new Value([$author]);
    }
}
