<?php

use B24\Project\Integration\Rest\Service;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Path;

/**
 * Миграция для REST
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

$em->registerEventHandler(
    'rest',
    'OnRestServiceBuildDescription',
    'b24.project',
    Service::class,
    'onRestServiceBuildDescription',
);

// запись даты версии в опцию (для миграции)
Option::set('b24.project', 'VERSION', 2026_11_10_15_30_00);