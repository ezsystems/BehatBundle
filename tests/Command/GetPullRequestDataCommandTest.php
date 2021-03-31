<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Test\Command;

use EzSystems\BehatBundle\Command\GetPullRequestDataCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GetPullRequestDataCommandTest extends TestCase
{
    private $commandTester;

    private const EXAMPLE_GITHUB_PR_LINK = 'https://github.com/ezsystems/BehatBundle/pull/99';

    public function setUp(): void
    {
        $this->commandTester = new CommandTester(new GetPullRequestDataCommand());
    }

    public function testProducesCorrectOutput()
    {
        $expectedOutput = 'https://github.com/katarzynazawada/BehatBundle EZP-30883 7.0.x-dev BehatBundle 2.5 1.3';
        $expectedReturnCode = 0;

        $this->commandTester->execute([
            'pull-request-url' => self::EXAMPLE_GITHUB_PR_LINK,
        ]);

        $this->assertEquals($expectedOutput, $this->commandTester->getDisplay());
        $this->assertEquals($expectedReturnCode, $this->commandTester->getStatusCode());
    }
}
