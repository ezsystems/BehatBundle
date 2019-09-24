<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use EzSystems\BehatBundle\API\Facade\ContentTypeFacade;

class ContentTypeContext implements Context
{
    /** @var ContentTypeFacade */
    private $contentTypeFacade;

    public function __construct(ContentTypeFacade $contentTypeFacade)
    {
        $this->contentTypeFacade = $contentTypeFacade;
    }

    /**
     * @Given I create a :contentTypeName Content Type in :contentTypeGroupName with :contentTypeIdentifier identifier
     */
    public function iCreateAContentTypeWithIdentifier($contentTypeName, $contentTypeGroupName, $contentTypeIdentifier, TableNode $fieldDetails): void
    {
        if ($this->contentTypeFacade->contentTypeExists($contentTypeIdentifier)) {
            return;
        }

        $fieldDefinitions = $this->parseFieldDefinitions($fieldDetails);
        $this->contentTypeFacade->createContentType($contentTypeName, $contentTypeIdentifier, $contentTypeGroupName, 'eng-GB', true, $fieldDefinitions);
    }

    private function parseFieldDefinitions(TableNode $fieldDetails): array
    {
        $parsedFields = [];
        $position = 10;

        foreach ($fieldDetails->getHash() as $fieldData) {
            $parsedFields[] = new FieldDefinitionCreateStruct([
                'fieldTypeIdentifier' => $this->contentTypeFacade->getFieldTypeIdentifierByName($fieldData['Field Type']),
                'identifier' => $fieldData['Identifier'],
                'names' => ['eng-GB' => $fieldData['Name']],
                'position' => $position,
                'isRequired' => $this->parseBool($fieldData['Required']),
                'isTranslatable' => $this->parseBool($fieldData['Translatable']),
                'isSearchable' => $this->parseBool($fieldData['Searchable']),
            ]);

            $position += 10;
        }

        return $parsedFields;
    }

    private function parseBool(string $value): bool
    {
        return $value === 'yes';
    }
}
