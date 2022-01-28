<?php
/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

use Ibexa\CodeStyle\PhpCsFixer\InternalConfigFactory;

$configFactory = new InternalConfigFactory();

return $configFactory
    ->buildConfig()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(
                array_filter([
                    __DIR__ . '/src',
                    __DIR__ . '/tests',
                ], 'is_dir')
            )
            ->files()->name('*.php')
    );
