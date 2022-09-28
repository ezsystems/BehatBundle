<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use EzSystems\Behat\API\Facade\ContentTypeFacade;

class ContentTypeContext implements Context
{
    /** @var \EzSystems\Behat\API\Facade\ContentTypeFacade */
    private $contentTypeFacade;

    public function __construct(ContentTypeFacade $contentTypeFacade)
    {
        $this->contentTypeFacade = $contentTypeFacade;
    }

    /**
     * @Given I create a :contentTypeName Content Type in :contentTypeGroupName with :contentTypeIdentifier identifier
     *
     * @param mixed $contentTypeName
     * @param mixed $contentTypeGroupName
     * @param mixed $contentTypeIdentifier
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
                'isRequired' => array_key_exists('Required', $fieldData) ? $this->parseBool($fieldData['Required']) : false,
                'isTranslatable' => array_key_exists('Translatable', $fieldData) ? $this->parseBool($fieldData['Translatable']) : false,
                'isSearchable' => array_key_exists('Searchable', $fieldData) ? $this->parseBool($fieldData['Searchable']) : false,
                'fieldGroup' => array_key_exists('Category', $fieldData) ? $fieldData['Category'] : null,
                'fieldSettings' => array_key_exists('Settings', $fieldData) ? $this->parseFieldSettings($fieldTypeIdentifier, $fieldData['Settings']) : null,
            ]);

            $position += 10;
        }

        return $parsedFields;
    }

    private function parseBool(string $value): bool
    {
        $value = strtolower($value);

        return 'yes' === $value || 'true' === $value;
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
        // Example: "QueryType-EzPlatformAdminUi:MediaSubtree,ContentType-folder,ItemsPerPage-100,Parameters-contentTypeId:folder;locationId:43
        $fields = explode(',', $settings);
        $parsedSettings['QueryType'] = explode('-', $fields[0])[1];
        $parsedSettings['ReturnedType'] = explode('-', $fields[1])[1];

        if (!empty($fields[2])) {
            $itemsPerPage = (int) explode('-', $fields[2])[1];
            $parsedSettings['ItemsPerPage'] = $itemsPerPage;
        }

        if (!empty($fields[3])) {
            $parameters = (string) explode('-', $fields[3])[1];
            foreach (explode(';', $parameters) as $parameterDefinition) {
                [$parameterKey, $parameterValue] = explode(':', $parameterDefinition);
                $parsedSettings['Parameters'][$parameterKey] = $parameterValue;
            }
        }

        return $parsedSettings;
    }
}
