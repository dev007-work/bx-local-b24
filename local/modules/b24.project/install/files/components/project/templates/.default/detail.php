<?php
defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'POPUP_COMPONENT_NAME'          => 'b24.project:project.detail',
        'POPUP_COMPONENT_TEMPLATE_NAME' => '.default',
        'POPUP_COMPONENT_PARAMS'        => [
            'PROJECT_ID'      => $arResult['VARIABLES']['PROJECT_ID'] ?? null,
            'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
        ],
        'USE_UI_TOOLBAR'                => 'Y',
        'RELOAD_GRID_AFTER_SAVE'        => true,
        'PAGE_MODE'                     => false,
        'PAGE_MODE_OFF_BACK_URL'        => $arResult['LIST_PAGE_URL']
    ]
);