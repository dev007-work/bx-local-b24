<?php

namespace B24\Project\Integration\UI\Filter;

use B24\Project\Integration\UI\FieldNameProvider;
use Bitrix\Main\Filter\Settings;

class ProjectSettings extends Settings
{
    private FieldNameProvider $fieldNameProvider;

    public function __construct(string $id)
    {
        parent::__construct(['ID' => $id]);

        $this->fieldNameProvider = new FieldNameProvider();
    }

    public function getFieldName(string $code): string
    {
        return $this->fieldNameProvider->getProjectFieldName($code);
    }
}