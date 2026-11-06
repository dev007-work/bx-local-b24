<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var CurrencyFieldComponent $component
 * @var array{value: array<string>, formattedValue: array<string>} $arResult
 */

print $component->getHtmlBuilder()->wrapSingleField(implode(',', $arResult['formattedValue']));