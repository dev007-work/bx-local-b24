<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;

/**
 * Миграция для создания таблицы истории изменений
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

$db->queryExecute(
    'CREATE TABLE `b24_academy_history`
        (
            `ID`                        int          NOT NULL AUTO_INCREMENT,
            `PROJECT_ID`                int          NOT NULL,
            `AUTHOR_ID`                 int          NOT NULL,
            `FIELD_NAME`                varchar(255) NOT NULL,
            `PREVIOUS_VALUE`            text,
            `CURRENT_VALUE`             text,
            `CHANGED_AT`                datetime     NOT NULL,
            PRIMARY KEY (`ID`)
        );'
);

// запись даты версии в опцию (для миграции)
Option::set('b24.project', 'VERSION', 2026_11_10_17_30_00);