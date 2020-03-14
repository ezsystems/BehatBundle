<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Test\Core\Log;

use EzSystems\Behat\Core\Log\LogFileReader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class LogFileReaderTest extends TestCase
{
    private const FILENAME = 'application.logs';

    /** @var vfsStreamDirectory */
    private $fileSystemRoot;

    /** @var LogFileReader */
    private $logReader;

    public function setUp(): void
    {
        $this->fileSystemRoot = vfsStream::setup();
        $this->logReader = new LogFileReader();
    }

    public function testReturnsEmptyArrayWhenThereAreNoLogs()
    {
        $file = vfsStream::newFile(self::FILENAME)
            ->at($this->fileSystemRoot);

        $logEntries = $this->logReader->getLastLines($file->url(), 5);

        Assert::assertEquals([], $logEntries);
    }

    public function testReturnsCorrectArrayWhenThereIsLessLogsThanLimit()
    {
        $file = vfsStream::newFile(self::FILENAME)
            ->withContent(<<<EOD
1
2
3
EOD
            )->at($this->fileSystemRoot);

        $logEntries = $this->logReader->getLastLines($file->url(), 5);

        Assert::assertEquals([1, 2, 3], $logEntries);
    }

    public function testReturnsCorrectArrayWhenThereIsMoreLogsThanLimit()
    {
        $file = vfsStream::newFile(self::FILENAME)
            ->withContent(<<<EOD
1
2
3
4
5
6
EOD
            )->at($this->fileSystemRoot);

        $logEntries = $this->logReader->getLastLines($file->url(), 5);

        Assert::assertEquals([2, 3, 4, 5, 6], $logEntries);
    }
}
