<?php

namespace B24\Project;

use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\Type\DateTime;

class DateRange
{
    public readonly ?DateTime $since;
    public readonly ?DateTime $until;

    public function __construct(?DateTime $since, ?DateTime $until)
    {
        if (isset($since, $until) && $since->getTimestamp() > $until->getTimestamp()) {
            throw new \InvalidArgumentException('Begining of date range cannot be more than ending.');
        }

        $this->since = $since;
        $this->until = $until;
    }

    public static function createFromArray(array $filter, string $fieldName): self
    {
        $sinceField = '>=' . $fieldName;
        if (!empty($filter[$sinceField])) {
            $since = DateTime::tryParse($filter[$sinceField]);
        }

        $untilField = '<=' . $fieldName;
        if (!empty($filter[$untilField])) {
            $until = DateTime::tryParse($filter[$untilField]);
        }

        return new DateRange($since ?? null, $until ?? null);
    }

    public function applyTo(ConditionTree $conditionTree, string $fieldName): void
    {
        if (isset($this->since)) {
            $conditionTree->where($fieldName, '>=', $this->since);
        }
        if (isset($this->until)) {
            $conditionTree->where($fieldName, '<=', $this->until);
        }
    }
}