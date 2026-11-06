<?php


use Bitrix\Main\UI\Extension;


defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array{availableCurrencies: array<string, string>, fieldName: string} $arResult
 * @var CurrencyFieldComponent $component
 */
Extension::load(['ui.dropdown', 'ui.forms']);

$isMultiple = $arResult['userField']['MULTIPLE'] === 'Y';

if ($isMultiple) {
    $values = $arResult['value'];
    $selectorClassName = 'ui-ctl ui-ctl-multiple-select';
} else {
    $values = (array) $arResult['value'];
    $selectorClassName = 'ui-ctl ui-ctl-after-icon ui-ctl-dropdown';
}

$options = [];
foreach ($arResult['availableCurrencies'] as $currency => $formattedName) {
    $optionAttributes = [];
    if (in_array($currency, $values)) {
        $optionAttributes[] = 'selected';
    }

    $options[] = sprintf(
        '<option class="ui-ctl-element" value="%s" %s>%s</option>',
        $currency,
        implode(' ', $optionAttributes),
        $formattedName
    );
}
$options = implode('', $options);

$selectorAttributes = [
    'name' => $arResult['fieldName'],
    'class' => $selectorClassName
];

if ($isMultiple) {
    $selectorAttributes['multiple'] = 'multiple';
}

$selectorAttributes = $component->getHtmlBuilder()->buildTagAttributes($selectorAttributes);
$html = <<<HTML
<select {$selectorAttributes}>
    {$options}
</select>
HTML;

print $component->getHtmlBuilder()->wrapSingleField($html);
