<?php

use B24\Academy\Crm\Deal\Observer;
use B24\Academy\Crm\Kanban\DealEntity;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Path;

/**
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

/**
 * Миграция для задания по созданию обработчика события
 * Создание собственного обработчика события для проверки перевода сделки в статус
 */

// обработчик события сделки
$em->registerEventHandler(
    'crm',
    'OnBeforeCrmDealUpdate',
    'b24.academy',
    Observer::class,
    'handleOnBeforeCrmDealUpdate',
);
// обработчик события канбана
$em->registerEventHandler(
    'main',
    'OnProlog',
    'b24.academy',
    DealEntity::class,
    'registerService',
);

// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_09_12_00_00);