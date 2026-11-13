<?php

namespace B24\Project\Integration\UI\Filter;

use B24\Project\Integration\UI\FieldNameProvider;
use Bitrix\Main\Filter\Settings;

class HistorySettings extends Settings
{
    private FieldNameProvider $fieldNameProvider;

    public function __construct(string $id, FieldNameProvider $fieldNameProvider)
    {
        parent::__construct(['ID' => $id]);

        $this->fieldNameProvider = new FieldNameProvider();
    }

    public function getFieldName(string $code): string
    {
        return $this->fieldNameProvider->getHistoryFieldName($code);
    }
}