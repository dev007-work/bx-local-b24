<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var CBitrixComponentTemplate $this
 * @var CurrencyFieldComponent $component
 * @var array{userField: array, fieldName: string, value: array} $arResult
 */

$component = $this->getComponent();
$arResult['items'] = $arResult['selectedItems'] = [];
foreach ($component->getAvailableCurrencies() as $currency => $formattedValue) {
    $item = [
        'id' => $currency,
        'title' => [
            'text' => $formattedValue,
            'type' => 'html'
        ],
        'entityId' => 'currency',
        'tabs' => ['main']
    ];
    if (in_array($currency, $arResult['value'])) {
        $arResult['selectedItems'][] = $item;
    }

    $arResult['items'][] = $item;
}
