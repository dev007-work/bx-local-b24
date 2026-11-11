<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;

/**
 * Миграция для создания таблицы своей сущности CRM
 *
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

$db->queryExecute(
    'CREATE TABLE `b24_academy_project`
        (
            `ID`                        int          NOT NULL AUTO_INCREMENT,
            `TITLE`                     varchar(255) NOT NULL,
            `CREATED_AT`                datetime     NOT NULL,
            `CREATED_BY`                int          NOT NULL,
            `UPDATED_AT`                datetime     NOT NULL,
            `UPDATED_BY`                int          NOT NULL,
            `ESTIMATED_COMPLETION_DATE` datetime,
            `COMPLETION_DATE`           datetime,
            `DESCRIPTION`               text,
            `RESPONSIBLE_ID`            int          NOT NULL,
            `COMMENT`                   text,
            `INCOME`                    varchar(255),
            PRIMARY KEY (`ID`)
        );'
);


// запись даты версии в опцию (для миграции)
Option::set('b24.project', 'VERSION', 2026_11_10_12_30_00);