<?php

use B24\Project\Integration\UI\SidePanel\RuleInjector;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;

/**
 * Миграция для доработки слайдера (боковой панели)
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

$em->registerEventHandler(
    'main',
    'OnEpilog',
    'b24.project',
    RuleInjector::class,
    'injextAnchorRules',
);

// запись даты версии в опцию (для миграции)
Option::set('b24.project', 'VERSION', 2026_11_10_16_30_00);