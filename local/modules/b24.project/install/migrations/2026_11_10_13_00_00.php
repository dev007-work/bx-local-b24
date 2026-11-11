<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\UrlRewriter;

/**
 * Миграция для создания правила маршрутизации
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

UrlRewriter::add('s1', [
    'ID'        => 'b24.project:project',
    'CONDITION' => '#^/invest/#',
    'PATH'      => '/invest/index.php',
]);

// запись даты версии в опцию (для миграции)
Option::set('b24.project', 'VERSION', 2026_11_10_13_30_00);