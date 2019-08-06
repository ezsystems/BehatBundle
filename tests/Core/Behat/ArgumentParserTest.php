<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Test\Core\Behat;


use EzSystems\Behat\Core\Behat\ArgumentParser;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ArgumentParserTest extends TestCase
{
    /**
     * @dataProvider provideUrlData
     */
    public function testParserGivenUrlCorrectly(string $valueToParse, string $expectedResult)
    {
        $parser = new ArgumentParser();

        $actualResult = $parser->parseUrl($valueToParse);

        Assert::assertEquals($expectedResult, $actualResult);
    }

    public function provideUrlData()
    {
        return [
            ['', '/'],
            ['root', '/'],
            ['/', '/'],
            ['/Home', '/Home'],
            ['Home', '/Home'],
            ['/New Folder', '/New-Folder'],
            ['New Folder', '/New-Folder'],
            ['New Folder Long Name', '/New-Folder-Long-Name'],
        ];
    }

}