<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Behat;

use Behat\Gherkin\Node\TableNode;

class TableNodeExtension extends TableNode
{
    /**
     * Adds a column (in form: ['header' => [values]] or ['header' => 'value']) to a given table.
     *
     * @throws \Behat\Gherkin\Exception\NodeException
     */
    public static function addColumn(TableNode $table, array $columnData): TableNode
    {
        $headers = array_keys($columnData);

        $newParameters = $table->getTable();

        foreach ($headers as $header) {
            $row = array_keys($table->getTable())[0];
            $newParameters[$row++][] = $header;
            if (\is_array($columnData[$header])) {
                foreach ($columnData[$header] as $value) {
                    $newParameters[$row++][] = $value;
                }
            } else {
                $newParameters[$row][] = $columnData[$header];
            }
        }

        return new self($newParameters);
    }

    /**
     * Removes a column from a Table.
     *
     * @throws \Behat\Gherkin\Exception\NodeException
     */
    public static function removeColumn(TableNode $table, string $columnName): TableNode
    {
        $newTable = [];
        $columns = array_flip(current($table->getTable()));

        if (!in_array($columnName, $columns)) {
            throw new \InvalidArgumentException(sprintf('Column: %s not found. Available columns are: %s', $columnName, implode(',', $columns)));
        }

        $columnPosition = $columns[$columnName];

        foreach ($table->getTable() as $row) {
            unset($row[$columnPosition]);
            $newTable[] = array_values($row);
        }

        return new self($newTable);
    }
}
