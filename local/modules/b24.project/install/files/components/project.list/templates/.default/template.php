<?php

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

Extension::load('ui.dialogs.messagebox');
Extension::load('ui.notification');

$APPLICATION->SetTitle('Список проектов');

foreach ($arResult['toolbar']['buttons'] as $button) {
    Toolbar::addButton($button);
}

Toolbar::addFilter($arResult['filter']);

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    $arResult['grid'],
);