<?php

namespace B24\Project;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class ProjectTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'b24_academy_project';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))->configurePrimary()->configureAutocomplete(),
            (new StringField('TITLE'))->configureRequired(),
            (new DatetimeField('CREATED_AT'))->configureRequired(),
            (new IntegerField('CREATED_BY')),
            (new DatetimeField('UPDATED_AT')),
            (new IntegerField('UPDATED_BY')),
            (new DatetimeField('ESTIMATED_COMPLETION_DATE')),
            (new DatetimeField('COMPLETION_DATE')),
            (new TextField('DESCRIPTION')),
            (new IntegerField('RESPONSIBLE_ID'))->configureRequired(),
            (new TextField('COMMENT')),
            (new StringField('INCOME')),
        ];
    }
}