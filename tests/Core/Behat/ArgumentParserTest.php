<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Test\Core\Behat;

use EzSystems\Behat\API\Facade\RoleFacade;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use Ibexa\Behat\Browser\Environment\ParameterProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ArgumentParserTest extends TestCase
{
    /**
     * @dataProvider provideUrlData
     */
    public function testParserGivenUrlCorrectly(string $valueToParse, string $expectedResult)
    {
        $roleFacadeStub = $this->createMock(RoleFacade::class);
        $parameterProviderStub = $this->createMock(ParameterProvider::class);

        $parser = new ArgumentParser($roleFacadeStub, $parameterProviderStub);

        $actualResult = $parser->parseUrl($valueToParse);

        Assert::assertEquals($expectedResult, $actualResult);
    }

    public static function provideUrlData()
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
            ['email@example.com', '/email-example.com'],
            ['first keyword, second', '/first-keyword-second'],
            ['2:45:00 pm', '/2-45-00-pm'],
        ];
    }
}
