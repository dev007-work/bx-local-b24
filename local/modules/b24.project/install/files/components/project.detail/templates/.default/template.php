<?php

use Bitrix\UI\Toolbar\Facade\Toolbar;

defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

foreach ($arResult['toolbar']['buttons'] as $button) {
    Toolbar::addButton($button);
}

$APPLICATION->SetTitle('Проект');
$APPLICATION->IncludeComponent(
    'bitrix:ui.form',
    '',
    $arResult['form'],
);
?>
<script>
    BX.ready(function () {
        const manager = new BX.B24.Project.Detail.Manager();
        manager.bindEvent();
    });
</script>
