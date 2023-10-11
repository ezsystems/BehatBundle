<?php

namespace Ibexa\Behat\ErrorHandler;

use Symfony\Component\Runtime\Internal\BasicErrorHandler;

class IbexaErrorHandler extends BasicErrorHandler
{
    public static function register(bool $debug): void
    {
        parent::register($debug);

        if (PHP_VERSION_ID > 80200) {
            error_reporting(E_ALL & ~E_DEPRECATED);
        }
    }
}