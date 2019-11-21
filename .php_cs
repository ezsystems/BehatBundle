<?php

return  EzSystems\EzPlatformCodeStyle\PhpCsFixer\EzPlatformInternalConfigFactory::build()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->files()->name('*.php')
    )
;