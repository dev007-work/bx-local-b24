<?php

namespace B24\Project\Integration\UI;

use Bitrix\Main\Localization\Loc;

class FieldNameProvider
{
    public function __construct()
    {
        Loc::loadLanguageFile(__FILE__);
    }

    public function getProjectFieldName(string $code): string
    {
        return Loc::getMessage("PROJECT_{$code}_FIELD_LABEL") ?? $code;
    }
}