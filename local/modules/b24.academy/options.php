<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

$modileId = basename(__DIR__);
global $APPLICATION, $USER;

Loader::includeModule($modileId);

$options = [
    'settings' => [],
];
$tabs = [
	[
		'DIV'   => 'settings',
		'TAB'   => 'Настройки',
		'TITLE' => 'Настройки',
	],
];
$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();

/** @var string $mid module id from GET */
?>
<form method="POST" action="<?=$APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
	<?= bitrix_sessid_post() ?>
    <? foreach ($options as $optionGroup) { ?>
        <? $tabControl->BeginNextTab() ?>
        <? __AdmSettingsDrawList($modileId, $optionGroup); ?>
    <? } ?>
	<? $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]) ?>
	<? $tabControl->End() ?>
</form>
