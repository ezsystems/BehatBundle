<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Subscriber;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use EzSystems\Behat\Core\Log\TestLogProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EndScenarioSubscriber implements EventSubscriberInterface
{
    private const PRIORITY = -1000;

    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::AFTER => ['resetLogProvider', self::PRIORITY],
            ExampleTested::AFTER => ['resetLogProvider', self::PRIORITY],
        ];
    }

    public function resetLogProvider(): void
    {
        TestLogProvider::reset();
    }
}
