<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use EzSystems\Behat\API\Facade\ContentTypeFacade;

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
            $fieldTypeIdentifier = $this->contentTypeFacade->getFieldTypeIdentifierByName($fieldData['Field Type']);

            $parsedFields[] = new FieldDefinitionCreateStruct([
                'fieldTypeIdentifier' => $fieldTypeIdentifier,
                'identifier' => $fieldData['Identifier'],
                'names' => ['eng-GB' => $fieldData['Name']],
                'position' => $position,
                'isRequired' => $this->parseBool($fieldData['Required']),
                'isTranslatable' => $this->parseBool($fieldData['Translatable']),
                'isSearchable' => $this->parseBool($fieldData['Searchable']),
                'fieldSettings' => array_key_exists('Settings', $fieldData) ? $this->parseFieldSettings($fieldTypeIdentifier, $fieldData['Settings']) : null,
            ]);

            $position += 10;
        }

        return $parsedFields;
    }

    private function parseBool(string $value): bool
    {
        $value = strtolower($value);

        return $value === 'yes' || $value === 'true';
    }

    private function parseFieldSettings(string $fieldTypeIdentifier, string $settings)
    {
        $parsedSettings = [];
        // TODO: Clean this up in the future if needed
        switch ($fieldTypeIdentifier) {
            case 'ezcontentquery':
                return $this->parseContentQuerySettings($settings);
            case 'ezmatrix':
                return $this->parseMatrixSettings($settings);
            case 'ezselection':
                return $this->parseSelectionSettings($settings);
            default:
                return $parsedSettings;
        }
    }

    private function parseMatrixSettings(string $settings): array
    {
        //Example: min_rows:5,Columns:col1-col2-col3
        $fields = explode(',', $settings);
        $minRows = (int) explode(':', $fields[0])[1];
        $parsedSettings['minimum_rows'] = $minRows;
        $columns = explode('-', explode(':', $fields[1])[1]);
        foreach ($columns as $column) {
            $parsedSettings['columns'][] = ['identifier' => $column, 'name' => $column];
        }

        return $parsedSettings;
    }

    private function parseSelectionSettings(string $settings): array
    {
        // Example: "is_multiple:false,options:Value1-Value2-Value3"
        $fields = explode(',', $settings);
        $isMultiple = $this->parseBool(explode(':', $fields[0])[1]);
        $options = explode(':', $fields[1])[1];
        $parsedOptions = array_values(explode('-', $options));
        $parsedSettings['isMultiple'] = $isMultiple;
        $parsedSettings['options'] = $parsedOptions;

        return $parsedSettings;
    }

    private function parseContentQuerySettings(string $settings): array
    {
        // Example: "QueryType-EzPlatformAdminUi:MediaSubtree,ContentType-folder"
        $fields = explode(',', $settings);
        $parsedSettings['QueryType'] = explode('-', $fields[0])[1];
        $parsedSettings['ReturnedType'] = explode('-', $fields[1])[1];

        return $parsedSettings;
    }
}
