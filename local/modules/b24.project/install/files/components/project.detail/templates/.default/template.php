<?php
defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

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
