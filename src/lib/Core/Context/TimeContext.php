<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Context;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Symfony\Component\Stopwatch\Stopwatch;

class TimeContext implements Context
{
    private $stopwatch;

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * @Given I wait :number seconds
     */
    public function iWait(string $number): void
    {
        $number = (int) $number;
        sleep($number);
    }

    /**
     * @Given I start measuring time
     */
    public function iStartMeasuingTime(): void
    {
        $this->stopwatch->start('event');
    }

    /**
     * @Given the action took no longer than :seconds seconds
     */
    public function actionTookNoLongerThan(string $maxDuration): void
    {
        $this->stopwatch->stop('event');
        $actualDuration = $this->stopwatch->getEvent('event')->getDuration() / 1000;
        $this->stopwatch->reset();

        Assert::assertLessThanOrEqual($maxDuration, $actualDuration);
    }

    /**
     * @Given the action took longer than :seconds seconds
     */
    public function actionTookLongerThan(string $maxDuration): void
    {
        $this->stopwatch->stop('event');
        $actualDuration = $this->stopwatch->getEvent('event')->getDuration() / 1000;
        $this->stopwatch->reset();

        Assert::assertGreaterThan($maxDuration, $actualDuration);
    }
}
