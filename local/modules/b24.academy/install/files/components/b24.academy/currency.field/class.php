<?php

use B24\Academy\UI\UserField\CurrencyField;
use Bitrix\Main\Component\BaseUfComponent;
use Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die;

class CurrencyFieldComponent extends BaseUfComponent
{
    public function __construct($component = null)
    {
        Loader::requireModule('currency');

        parent::__construct($component);
    }

    public function getAvailableCurrencies(): array
    {
        return CurrencyField::getFormattedCurrenciesList($this->userField);
    }

    public function formatCurrency(string $currency): string
    {
        return CurrencyField::formatCurrency($this->userField, CurrencyField::getCurrencyByName($currency));
    }

    protected static function getUserTypeId(): string
    {
        return CurrencyField::USER_TYPE_ID;
    }
}