<?php

use B24\Academy\UI\UserField\CurrencyField;
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
 * Миграция для задания по созданию типа поля
 * Создание собственного типа пользовательского поля для сущности CRM
 */

// обработчик события
$em->registerEventHandler(
    'main',
    'OnUserTypeBuildList',
    'b24.academy',
    CurrencyField::class,
    'getUserTypeDescription',
);

// копирование файлов
CopyDirFiles(
    Path::combine(__DIR__, '/../files/components/b24.academy'),
    Path::convertRelativeToAbsolute('/local/components/b24.academy'),
    true,
    true
);
CopyDirFiles(
    Path::combine(__DIR__, '/../templates/.default/components/bitrix/crm.field.filter'),
    Path::convertRelativeToAbsolute('/local/templates/.default/components/bitrix/crm.field.filter'),
    true,
    true
);

// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_06_12_00_00);