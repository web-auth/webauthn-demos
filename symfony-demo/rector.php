<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets(php84: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, typeDeclarations: true)
    ->withComposerBased(symfony: true, doctrine: true, phpunit: true)
    ->withSets([SymfonySetList::SYMFONY_CODE_QUALITY, SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION])
;
