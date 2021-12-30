<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use RuntimeException;

class FileContext implements Context
{
    private const SOURCE_FILE_DIRECTORY = 'vendor/ezsystems/behatbundle/src/lib/Data';
    /** @var string */
    private $projectDirectory;

    public function __construct($projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @Given I create a file :path with content from :sourceFile
     *
     * @param mixed $path
     * @param mixed $sourceFile
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
     *
     * @param mixed $file
     * @param mixed $sourceFile
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

    /**
     * @Given I apply the patch
     */
    public function patchFile(PyStringNode $patchContent)
    {
        $this->createFileFromContent($this->projectDirectory . \DIRECTORY_SEPARATOR . 'patch.patch', $patchContent);
        $command = sprintf('patch -d %s -i %s -Np1', $this->projectDirectory, 'patch.patch');
        $output = [];
        $result_code = 0;
        exec($command, $output, $result_code);

        if (!$this->isPatchSuccessFull($result_code, $output)) {
            throw new RuntimeException('Failed applying patch');
        }
    }

    /**
     * @Given I define the :variableName with value :value in .env file
     */
    public function defineEnvVariable(string $variableName, string $value): void
    {
        $dotEnvDilePath = $this->projectDirectory . \DIRECTORY_SEPARATOR . '.env';
        file_put_contents($dotEnvDilePath, sprintf('%s=%s' . PHP_EOL, $variableName, $value), FILE_APPEND);
    }

    private function createDirectoryStructure($destinationPath): void
    {
        $directoryStructure = \dirname($destinationPath);
        if (!file_exists($directoryStructure)) {
            mkdir($directoryStructure, 0777, true);
        }
    }

    /**
     * @param string[] $output
     */
    private function isPatchSuccessFull(int $result_code, array $output): bool
    {
        return $result_code === 0 && empty(array_filter($output, static function (string $outputLine) {
            return strpos($outputLine, 'failed') !== false;
        }));
    }
}
