<?php

namespace B24\Project\Integration\UI\Filter;

use B24\Project\DateRange;
use Bitrix\Main\Filter\DataProvider;
use Bitrix\Main\UI\Filter\FieldAdapter;

class ProjectDataProvider extends DataProvider
{
    public function __construct(
        private readonly ProjectSettings $projectSettings,
    )
    {}

    public function getSettings(): ProjectSettings
    {
        return $this->projectSettings;
    }

    /**
     * Подготовка списка полей для фильтра
     *
     * @return array
     */
    public function prepareFields(): array
    {
        return [
            'TITLE' => $this->createField(
                'TITLE',
                [
                    'name'    => $this->projectSettings->getFieldName('TITLE'),
                    'type'    => FieldAdapter::STRING,
                    'default' => true,
                ]
            ),
            'CREATED_AT' => $this->createField(
                'CREATED_AT',
                [
                    'name'    => $this->projectSettings->getFieldName('CREATED_AT'),
                    'type'    => FieldAdapter::DATE,
                    'default' => false,
                ]
            ),
            'ESTIMATED_COMPLETION_DATE' => $this->createField(
                'ESTIMATED_COMPLETION_DATE',
                [
                    'name'    => $this->projectSettings->getFieldName('ESTIMATED_COMPLETION_DATE'),
                    'type'    => FieldAdapter::DATE,
                    'default' => true,
                    'partial' => true,
                ]
            ),
            'COMPLETION_DATE' => $this->createField(
                'COMPLETION_DATE',
                [
                    'name'    => $this->projectSettings->getFieldName('COMPLETION_DATE'),
                    'type'    => FieldAdapter::DATE,
                    'default' => true,
                ]
            ),
            'RESPONSIBLE_ID' => $this->createField(
                'RESPONSIBLE_ID',
                [
                    'name'    => $this->projectSettings->getFieldName('RESPONSIBLE_ID'),
                    'type'    => FieldAdapter::ENTITY_SELECTOR,
                    'default' => true,
                    'partial' => true,
                ]
            ),
        ];
    }

    /**
     * Подготовка данных для поля фильтра
     * Например, получение значений для выпадающего списка или выбора сущности
     *
     * @param $fieldID
     * @return array|null
     */
    public function prepareFieldData($fieldID): ?array
    {
        return match ($fieldID) {
            'RESPONSIBLE_ID' => [
                'params' => [
                    'dialogOptions' => [
                        'entities' => [
                            [
                                'id' => 'user'
                            ]
                        ],
                        'multiple' => true
                    ],
                    'multiple' => true
                ]
            ],
            default => null
        };
    }

    /**
     * Подготовка значений установленных полей фильтра
     *
     * @param array $rawFilterValue
     * @return array
     */
    public function prepareFilterValue(array $rawFilterValue): array
    {
        foreach ($this->prepareFields() as $field) {
            $rawFilterValue[$field->getId()] = match ($field->getType()) {
                FieldAdapter::DATE => DateRange::createFromArray($rawFilterValue, $field->getId()),
                default => $rawFilterValue[$field->getId()]
            };
        }

        return $rawFilterValue;
    }
}