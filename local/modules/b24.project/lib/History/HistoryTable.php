<?php

namespace B24\Project\History;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HistoryTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'b24_academy_history';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))->configurePrimary()->configureAutocomplete(),
            (new IntegerField('PROJECT_ID'))->configureRequired(),
            (new IntegerField('AUTHOR_ID'))->configureRequired(),
            (new StringField('FIELD_NAME'))->configureRequired(),
            (new TextField('PREVIOUS_VALUE')),
            (new TextField('CURRENT_VALUE')),
            (new DatetimeField('CHANGED_AT'))->configureRequired()
        ];
    }
}