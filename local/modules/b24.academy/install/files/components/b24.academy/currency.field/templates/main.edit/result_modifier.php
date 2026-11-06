<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var CBitrixComponentTemplate $this
 * @var CurrencyFieldComponent $component
 * @var array $arResult
 */

$component = $this->getComponent();
$arResult['availableCurrencies'] = $component->getAvailableCurrencies();