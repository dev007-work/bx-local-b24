<?php

namespace B24\Project;

use Bitrix\Main\ORM\Query\Filter\ConditionTree;

class Filter
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?DateRange $createdAt,
        public readonly ?array $responsible,
        public readonly ?DateRange $estimatedCompletionDate,
        public readonly ?DateRange $completionDate,
    )
    {}

    public function toCriteria(): ConditionTree
    {
        $criteria = new ConditionTree();

        if (isset($this->title)) {
            $title = str_replace('%', '%%', $this->title);
            $criteria->whereLike('TITLE', "%{$title}%");
        }

        if (isset($this->createdAt)) {
            $this->createdAt->applyTo($criteria, 'CREATED_AT');
        }

        if (!empty($this->responsible)) {
            $criteria->whereIn('RESPONSIBLE_ID', $this->responsible);
        }

        if (isset($this->estimatedCompletionDate)) {
            $this->estimatedCompletionDate->applyTo($criteria, 'ESTIMATED_COMPLETION_DATE');
        }

        if (isset($this->completionDate)) {
            $this->completionDate->applyTo($criteria, 'COMPLETION_DATE');
        }

        return $criteria;
    }
}