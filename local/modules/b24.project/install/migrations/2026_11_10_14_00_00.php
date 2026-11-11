<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Path;

/**
 * Миграция для копирования файлов
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

// копирование файлов компонентов
CopyDirFiles(
    Path::combine(__DIR__, '..', '/files/components/'),
    Path::convertRelativeToAbsolute(Path::combine('local/components/b24.project/')),
    true,
    true
);

// копирование файлов публичной части
CopyDirFiles(
    Path::combine(__DIR__, '..', '/files/public/'),
    Application::getDocumentRoot(),
    true,
    true
);

// запись даты версии в опцию (для миграции)
//Option::set('b24.project', 'VERSION', 2026_11_10_14_30_00);