<?php

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
 * Миграция для задания по созданию действия БП
 * Создание собственного действия "Ожидать изменений документа" бизнес-пройесса
 */

// копирование файлов
CopyDirFiles(
    Path::combine(__DIR__, '/../files/activities/'),
    Path::convertRelativeToAbsolute('/local/activities/'),
    true,
    true
);

// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_07_12_00_00);