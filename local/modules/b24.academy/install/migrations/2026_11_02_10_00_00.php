<?php

use B24\Academy\UI\Tasks\ListPageMutator;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;

/**
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

// копирование файлов стилей
CopyDirFiles(__DIR__ . '/../files/css', $docRoot . '/local/css', true, true);
// регистрация обработчика, подключающего стили в эпилоге
$em->registerEventHandler(
    'main',
    'OnEpilog',
    'b24.academy',
    ListPageMutator::class,
    'handleOnEpilog'
);
// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_03_10_00_00);