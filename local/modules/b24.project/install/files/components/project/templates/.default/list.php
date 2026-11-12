<?php
defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$APPLICATION->IncludeComponent(
    'b24.project:project.list',
    '',
    [
        'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
    ]
);