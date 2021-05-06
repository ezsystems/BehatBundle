<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

class FileContext implements Context
{
    /** @var string */
    private $projectDirectory;

    private const SOURCE_FILE_DIRECTORY = 'vendor/ezsystems/behatbundle/src/lib/Data';

    public function __construct($projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @Given I create a file :path with content from :sourceFile
     */
    public function createFileFromSourceFile($path, $sourceFile): void
    {
        $content = file_get_contents(sprintf('%s/%s/%s', $this->projectDirectory, self::SOURCE_FILE_DIRECTORY, $sourceFile));
        $destinationPath = sprintf('%s/%s', $this->projectDirectory, $path);
        $this->createDirectoryStructure($destinationPath);
        file_put_contents($destinationPath, $content);
    }

    /**
     * @Given I append to :file file :sourcePath
     */
    public function appendToFile($file, $sourceFile): void
    {
        $content = file_get_contents(sprintf('%s/%s/%s', $this->projectDirectory, self::SOURCE_FILE_DIRECTORY, $sourceFile));
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }

    /**
     * @Given I create a file :path with contents
     */
    public function createFileFromContent(string $path, PyStringNode $fileContent): void
    {
        $destinationPath = sprintf('%s/%s', $this->projectDirectory, $path);
        $this->createDirectoryStructure($destinationPath);
        file_put_contents($destinationPath, $fileContent->getRaw());
    }

    private function createDirectoryStructure($destinationPath): void
    {
        $directoryStructure = \dirname($destinationPath);
        if (!file_exists($directoryStructure)) {
            mkdir($directoryStructure, 0777, true);
        }
    }
}
