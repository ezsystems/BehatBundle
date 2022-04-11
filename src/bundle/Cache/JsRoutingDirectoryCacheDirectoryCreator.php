<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class JsRoutingDirectoryCacheDirectoryCreator implements CacheWarmerInterface
{
    private const FOS_JS_ROUTING_CACHE_DIR = 'fosJsRouting';

    public function isOptional()
    {
        return true;
    }

    public function warmUp(string $cacheDir)
    {
        // Workaround for https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/pull/434
        $cachePath = $cacheDir . \DIRECTORY_SEPARATOR . self::FOS_JS_ROUTING_CACHE_DIR;
        if (!file_exists($cachePath) && !mkdir($cachePath) && !is_dir($cachePath)) {
            throw new \RuntimeException('Unable to create JsRoutingBundle cache directory ' . $cachePath);
        }
    }
}
