<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array $arResult
 */

$values = [];
if (isset($arResult['additionalParameters']['bVarsFromForm']) && $arResult['additionalParameters']['bVarsFromForm']) {
    $values['format'] = $GLOBALS[$arResult['additionalParameters']['NAME']]['FORMAT'] ?? '';
} elseif (isset($arResult['userField']) && $arResult['userField']) {
    $values['format'] = $arResult['userField']['SETTINGS']['FORMAT'];
} else {
    $values['format'] = '#FULL_NAME# - #SYMBOL#';
}

$arResult['values'] = $values;