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

    private const EXAMPLE_GITHUB_PR_LINK = 'https://github.com/ezsystems/ezplatform-admin-ui/pull/1313';

    private const EXAMPLE_GITHUB_TOKEN = 'd0285ed5c8644f30547572ead2ed897431c1fc09';

    public function setUp(): void
    {
        $this->commandTester = new CommandTester(new GetPullRequestDataCommand());
    }

    public function testProducesCorrectOutput()
    {
        $expectedOutput = 'https://github.com/GrabowskiM/ezplatform-admin-ui EZP-31515-fix-flatpickr 2.0.x-dev ezplatform-admin-ui master master';
        $expectedReturnCode = 0;

        $this->commandTester->execute([
            'pull-request-url' => self::EXAMPLE_GITHUB_PR_LINK,
            'token' => self::EXAMPLE_GITHUB_TOKEN,
        ]);

        $this->assertEquals($expectedOutput, $this->commandTester->getDisplay());
        $this->assertEquals($expectedReturnCode, $this->commandTester->getStatusCode());
    }
}
