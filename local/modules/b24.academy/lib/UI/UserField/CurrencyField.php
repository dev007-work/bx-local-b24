<?php

namespace B24\Academy\UI\UserField;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\UserField\Types\BaseType;
use CUserTypeManager;
use http\Exception\RuntimeException;

class CurrencyField extends BaseType
{
    public const USER_TYPE_ID = 'currency';
    public const RENDER_COMPONENT = 'b24.academy:currency.field';

    public static array $currencyList = [];

    protected static function getDescription(): array
    {
        return [
            'DESCRIPTION' => 'Валюта',
            'BASE_TYPE'   => CUserTypeManager::BASE_TYPE_STRING,
        ];
    }

    public static function getDbColumnType(): string
    {
        return 'char(3)';
    }

    public static function prepareSettings(array $userField): array
    {
        return [
            'FORMAT' => $userField['SETTINGS']['FORMAT'] ?: '#SYMBOL# - #FULL_NAME#',
        ];
    }

    private static function loadCurrencies(): array
    {
        if (CurrencyField::$currencyList) {
            return CurrencyField::$currencyList;
        }

        $names = CurrencyManager::getNameList();
        $symbols = CurrencyManager::getSymbolList();

        $currencies = [];
        foreach ($names as $currency => $name) {
            $currencies[$currency] = [
                'FULL_NAME' => $name,
                'SYMBOL'    => $symbols[$currency] ?? $currency,
            ];
        }

        return CurrencyField::$currencyList = $currencies;
    }

    public static function formatCurrency(array $userField, array $currency): string
    {
        $placeholders = array_map(
            static fn(string $placeholder): string => '#' . $placeholder . '#',
            array_keys($currency)
        );

        return str_replace($placeholders, array_values($currency), $userField['SETTINGS']['FORMAT']);
    }

    public static function getFormattedCurrenciesList(array $userField): array
    {
        $formattedCurrencies = [];
        foreach (CurrencyField::loadCurrencies() as $currency => $data) {
            $formattedCurrencies[$currency] = CurrencyField::formatCurrency($userField, $data);
        }

        return $formattedCurrencies;
    }

    public static function onBeforeSave(array $userField, string $value): string
    {
        CurrencyField::validateCurrency($value);

        return $value;
    }

    public static function getDefaultValue(array $userField, array $additionalParameters = [])
    {
        return CurrencyManager::getBaseCurrency();
    }

    public static function validateCurrency(string $currency): void
    {
        if (!CurrencyManager::isCurrencyExist($currency)) {
            throw new RuntimeException('Unknown currency');
        }
    }

    public static function getCurrencyByName(string $currency): array
    {
        CurrencyField::validateCurrency($currency);

        $currencies = CurrencyField::loadCurrencies();

        return $currencies[$currency];
    }
}