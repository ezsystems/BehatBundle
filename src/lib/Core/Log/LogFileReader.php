<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Core\Log;

class LogFileReader
{
    public function getLastLines($filePath, $numberOfLines): array
    {
        $logEntries = [];
        $counter = 0;

        $file = @fopen($filePath, 'r');

        if (false === $file) {
            return [];
        }

        while (!feof($file)) {
            if ($counter >= $numberOfLines) {
                array_shift($logEntries);
            }

            $line = fgets($file);
            if ($line === false) {
                break;
            }

            $logEntries[] = str_replace(PHP_EOL, '', $line);
            ++$counter;
        }

        fclose($file);

        return $logEntries;
    }
}

class_alias(LogFileReader::class, 'EzSystems\Behat\Core\Log\LogFileReader');
