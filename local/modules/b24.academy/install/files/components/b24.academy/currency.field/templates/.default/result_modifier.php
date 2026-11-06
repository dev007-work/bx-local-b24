<?php

/**
 * @var CurrencyFieldComponent $component
 * @var array{value: array<string>|string} $arResult
 */

defined('B_PROLOG_INCLUDED') || die;

if ($component->isMultiple()) {
    $arResult['value'] = [$arResult['value']];
}

$arResult['formattedValue'] = [];
foreach ($arResult['value'] as $currencyId) {
    $arResult['formattedValue'][] = $component->formatCurrency($currencyId);
}