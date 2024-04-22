<?php

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Class_\SpatieEnumClassToEnumRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->skip([
        SpatieEnumClassToEnumRector::class,
    ]);
};
