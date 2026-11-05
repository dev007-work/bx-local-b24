<?php

use B24\Academy\UI\Menu\LeftMenuExtender;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

/**
 * @global Connection $db
 * @global EventManager $em
 * @global $docRoot
 * @global $modRoot
 */

Loader::includeModule('iblock');

/**
 * Миграция для задания по CSS / JS Кастомизации (часть 2)
 * Вывод в боковом меню Б24 фактов о компании из инфоблока
 */

// Создание типа инфоблока
$iblockType = new CIBlockType();
$typeId = 'b24_academy_customs';
$typeCheck = CIBlockType::GetByID($typeId)->Fetch();
if ($typeCheck === false) {
    $typeId = $iblockType->Add([
        'ID'       => $typeId,
        'SECTIONS' => 'N',
        'IN_RSS'   => 'N',
        'SORT'     => 100,
        'LANG'     => [
            'ru' => [
                'NAME'         => 'Академия Битрикс24',
                'ELEMENT_NAME' => 'Факт',
            ],
            'en' => [
                'NAME'         => 'Bitrix24 academy',
                'ELEMENT_NAME' => 'Fact',
            ],
        ],
    ]);
    if (!empty($iblockType->LAST_ERROR)) {
        throw new RuntimeException($iblockType->LAST_ERROR);
    }
}

// Создание инфоблока
$iblockManager = new CIBlock();
$iblockId = $iblockManager->Add([
    'ACTIVE'         => 'Y',
    'API_CODE'       => 'companyFacts',
    'NAME'           => 'Факты о компании',
    'IBLOCK_TYPE_ID' => $typeId,
    'LID'            => 's1',
    'WORKFLOW'       => 'N',
    'BIZPROC'        => 'N',
    'VERSION'        => 2,
    'CODE'           => 'b24_academy_company_facts',
]);
if (!empty($iblockManager->LAST_ERROR)) {
    throw new RuntimeException($iblockManager->LAST_ERROR);
}
Option::set('b24.academy', 'FACTS_IBLOCK_ID', $iblockId);

// копирование файлов
CopyDirFiles(__DIR__ . '/../files/components', $docRoot . '/local/components', true, true);
CopyDirFiles(__DIR__ . '/../files/js',         $docRoot . '/local/js',         true, true);

// обработчик события
$em->registerEventHandler(
    'main',
    'OnEpilog',
    'b24.academy',
    LeftMenuExtender::class,
    'handleOnEpilog',
);

// запись даты версии в опцию (для миграции)
Option::set('b24.academy', 'VERSION', 2026_11_05_12_00_00);