<?php

namespace B24\Project\History;

use B24\Project\DateRange;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;

class Filter
{
    public function __construct(
        public readonly ?int $projectId,
        public readonly ?array $fieldNames,
        public readonly ?string $previousValue,
        public readonly ?string $currentValue,
        public readonly ?DateRange $changedAt,
        public readonly ?array $authorIds,
    )
    {}

    public function toCriteria(): ConditionTree
    {
        $criteria = new ConditionTree();

        if (isset($this->projectId)) {
            $criteria->where('PROJECT_ID', '=', $this->projectId);
        }

        if (!empty($this->fieldNames)) {
            $criteria->whereIn('FIELD_NAME', $this->fieldNames);
        }

        if (isset($this->previousValue)) {
            $criteria->whereLike('PREVIOUS_VALUE', str_replace('%', '%%', $this->previousValue));
        }

        if (isset($this->currentValue)) {
            $criteria->whereLike('CURRENT_VALUE', str_replace('%', '%%', $this->currentValue));
        }

        if (isset($this->changedAt)) {
            $this->changedAt->applyTo($criteria, 'CHANGED_AT');
        }

        if (!empty($this->authorIds)) {
            $criteria->whereIn('AUTHOR_ID', $this->authorIds);
        }

        return $criteria;
    }
}