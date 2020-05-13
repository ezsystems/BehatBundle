<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Subscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractProcessStage
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher, UserService $userService, PermissionResolver $permissionResolver, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->logger = $logger;
        $this->validateTransitions();
    }

    abstract protected function getTransitions(): array;

    protected function transitionToNextStage($event)
    {
        $threshold = 0;
        $randomValue = $this->getRandomNumber();

        $chosenEvent = null;

        foreach ($this->getTransitions() as $eventName => $probability) {
            if ($threshold <= $randomValue && $randomValue < $threshold + $probability) {
                $chosenEvent = $eventName;
            }

            $threshold += $probability;
        }

        if (!$chosenEvent) {
            Assert::fail('No event was chosen, possible error in transition logic.');
        }

        $this->eventDispatcher->dispatch($chosenEvent, $event);
    }

    protected function setCurrentUser(string $user): void
    {
        $user = $this->userService->loadUserByLogin($user);
        $this->permissionResolver->setCurrentUserReference($user);
    }

    protected function getCurrentUser(): User
    {
        $userRef = $this->permissionResolver->getCurrentUserReference();
        return $this->userService->loadUser($userRef->getUserId());
    }

    protected function validateTransitions(): void
    {
        $sum = 0;
        foreach ($this->getTransitions() as $event => $probability) {
            $sum += $probability;
        }

        Assert::assertEquals(1, $sum, 'Sum of all probabilities must be equal to 1.');
    }

    protected function getRandomNumber(): float
    {
        return random_int(0, 999) / 1000;
    }

    protected function getRandomValue(array $values): string
    {
        return $values[array_rand($values, 1)];
    }
}
