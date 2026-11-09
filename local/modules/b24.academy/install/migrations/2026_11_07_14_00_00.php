<?php

use B24\Academy\Task\ActivityWatcher;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Type\Date;

/**
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

/**
 * Миграция для задания по созданию агента
 * Создание собственного агента по пингу задач без комментариев
 */

CAgent::AddAgent(
    ActivityWatcher::class . '::runAgent();',
    'b24.academy',
    'Y',
    60,
    '',
    'Y',
    (new Date())->add('1d'),
);

// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_08_12_00_00);