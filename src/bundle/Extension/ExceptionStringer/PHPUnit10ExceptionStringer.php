<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Behat\Extension\ExceptionStringer;

use Behat\Testwork\Exception\Stringer\ExceptionStringer;
use Exception;

final class PHPUnit10ExceptionStringer implements ExceptionStringer
{
    public function supportsException(Exception $exception): bool
    {
        return $exception instanceof \PHPUnit\Framework\Exception
            && class_exists('PHPUnit\\Util\\ThrowableToStringMapper');
    }

    public function stringException(Exception $exception, $verbosity)
    {
        return trim(\PHPUnit\Util\ThrowableToStringMapper::map($exception));
    }
}
