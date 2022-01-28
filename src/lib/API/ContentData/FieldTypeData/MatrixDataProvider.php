<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\API\ContentData\FieldTypeData;

use EzSystems\Behat\API\ContentData\RandomDataGenerator;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\FieldTypeMatrix\FieldType\Value;
use Ibexa\FieldTypeMatrix\FieldType\Value\Row;

class MatrixDataProvider extends AbstractFieldTypeDataProvider
{
    private const MAX_NUMBER_OF_ITEMS = 200;

    private $contentTypeService;

    public function __construct(RandomDataGenerator $randomDataGenerator, ContentTypeService $contentTypeService)
    {
        parent::__construct($randomDataGenerator);
        $this->contentTypeService = $contentTypeService;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return 'ezmatrix' === $fieldTypeIdentifier;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        $this->setLanguage($language);

        $fieldDefinition = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->getFieldDefinition($fieldIdentifier);
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $minimumRows = $fieldSettings['minimum_rows'];
        $columnIdentifiers = array_column($fieldSettings['columns'], 'identifier');

        $numberOfEntriesToCreate = $this->getFaker()->numberBetween($minimumRows, self::MAX_NUMBER_OF_ITEMS);

        $rows = [];
        for ($i = 0; $i < $numberOfEntriesToCreate; ++$i) {
            $rows[] = $this->getRandomEntry($columnIdentifiers);
        }

        return new Value($rows);
    }

    /**
     * @param string $value Matrix data. Columns separated by ":" and rows separated by ",". First row is for column indices.
     *                      For a 3 column table:
     *                      Col1:Col2:col3,value11:value12:value13,value21:value22:value23,value31:value32:value33
     *
     * The result would be parsed as:
     *        Col1    Col2    Col3
     * Row1: value11 value12 value13
     * Row2: value21 value22 value23
     * Row3: value31 value32 value33
     */
    public function parseFromString(string $value)
    {
        $rows = explode(',', $value);

        $columnIdentifiers = explode(':', array_shift($rows));
        $numberOfColumns = count($columnIdentifiers);

        $parsedRows = [];
        foreach ($rows as $row) {
            $parsedRow = [];
            $columnValues = explode(':', $row);
            for ($i = 0; $i < $numberOfColumns; ++$i) {
                $parsedRow[$columnIdentifiers[$i]] = $columnValues[$i];
            }

            $parsedRows[] = new Row($parsedRow);
        }

        return new Value($parsedRows);
    }

    private function getRandomEntry($columnIdentifiers)
    {
        $values = [];
        foreach ($columnIdentifiers as $columnIdentifier) {
            $values[$columnIdentifier] = $this->getFaker()->words(3, true);
        }

        return new Row($values);
    }
}
