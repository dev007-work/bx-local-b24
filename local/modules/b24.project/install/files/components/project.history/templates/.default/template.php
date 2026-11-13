<?php

use Bitrix\UI\Toolbar\Facade\Toolbar;

defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$APPLICATION->SetTitle('История изменений');

Toolbar::addFilter($arResult['filter']);

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    $arResult['grid'],
);