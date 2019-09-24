<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Test\Helper;

use EzSystems\BehatBundle\API\Facade\RoleFacade;
use EzSystems\BehatBundle\Helper\ArgumentParser;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ArgumentParserTest extends TestCase
{
    /**
     * @dataProvider provideUrlData
     */
    public function testParserGivenUrlCorrectly(string $valueToParse, string $expectedResult)
    {
        $roleFacadeStub = $this->createMock(RoleFacade::class);

        $parser = new ArgumentParser($roleFacadeStub);

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
