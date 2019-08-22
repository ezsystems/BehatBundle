<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Behat\Core\Behat;

class ArgumentParser
{
    public function parseUrl(string $url)
    {
        if ($url === 'root') {
            return '/';
        }

        $url = str_replace(' ', '-', $url);

        return strpos($url, '/') === 0 ? $url : sprintf('/%s', $url);
    }
}
