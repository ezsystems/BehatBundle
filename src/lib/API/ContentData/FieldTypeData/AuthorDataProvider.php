<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;

class AuthorDataProvider extends RandomDataGenerator implements FieldTypeDataProviderInterface
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezauthor';
    }

    public function generateData(string $language = 'eng-GB')
    {
        return new Value([$this->getSingleAuthor($language), $this->getSingleAuthor($language)]);
    }

    private function getSingleAuthor(string $language = 'eng-GB'): Author
    {
        $author = new Author();

        $this->setLanguage($language);
        $author->name = $this->getFaker()->name;
        $author->email = $this->getFaker()->email;

        return $author;
    }
}
