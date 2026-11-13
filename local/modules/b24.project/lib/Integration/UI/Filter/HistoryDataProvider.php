<?php

namespace B24\Project\Integration\UI\Filter;

use B24\Project\DateRange;
use Bitrix\Main\Filter\DataProvider;
use Bitrix\Main\UI\Filter\FieldAdapter;

class HistoryDataProvider extends DataProvider
{
    public function __construct(
        private readonly ProjectDataProvider $projectDataProvider,
        private readonly HistorySettings $historySettings,
    )
    {}

    public function getSettings(): HistorySettings
    {
        return $this->historySettings;
    }

    /**
     * Подготовка списка полей для фильтра
     *
     * @return array
     */
    public function prepareFields(): array
    {
        return [
            'ID' => $this->createField(
                'ID',
                [
                    'name'    => $this->historySettings->getFieldName('ID'),
                    'type'    => FieldAdapter::NUMBER,
                    'default' => false,
                ]
            ),
            'PROJECT_ID' => $this->createField(
                'PROJECT_ID',
                [
                    'name'    => $this->historySettings->getFieldName('PROJECT_ID'),
                    'type'    => FieldAdapter::NUMBER,
                    'default' => false,
                ]
            ),
            'AUTHOR_ID' => $this->createField(
                'AUTHOR_ID',
                [
                    'name'    => $this->historySettings->getFieldName('AUTHOR_ID'),
                    'type'    => FieldAdapter::ENTITY_SELECTOR,
                    'default' => false,
                    'partial' => true,
                ]
            ),
            'FIELD_NAME' => $this->createField(
                'FIELD_NAME',
                [
                    'name'    => $this->historySettings->getFieldName('FIELD_NAME'),
                    'type'    => FieldAdapter::LIST,
                    'default' => false,
                    'partial' => true,
                ]
            ),
            'PREVIOUS_VALUE' => $this->createField(
                'PREVIOUS_VALUE',
                [
                    'name'    => $this->historySettings->getFieldName('PREVIOUS_VALUE'),
                    'type'    => FieldAdapter::TEXTAREA,
                    'default' => false,
                ]
            ),
            'CURRENT_VALUE' => $this->createField(
                'CURRENT_VALUE',
                [
                    'name'    => $this->historySettings->getFieldName('CURRENT_VALUE'),
                    'type'    => FieldAdapter::TEXTAREA,
                    'default' => false,
                ]
            ),
            'CHANGED_AT' => $this->createField(
                'CHANGED_AT',
                [
                    'name'    => $this->historySettings->getFieldName('CHANGED_AT'),
                    'type'    => FieldAdapter::DATE,
                    'default' => false,
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
        if ($fieldID === 'FIELD_NAME') {
            $projectFields = [];
            foreach ($this->projectDataProvider->prepareFields() as $field) {
                $projectFields[$field->getId()] = $field->getName();
            }

            return [
                'items'  => $projectFields,
                'params' => [
                    'multiple' => true
                ]
            ];
        }

        if ($fieldID === 'AUTHOR_ID') {
            return [
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
            ];
        }

        return null;
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