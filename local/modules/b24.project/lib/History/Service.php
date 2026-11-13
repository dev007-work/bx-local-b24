<?php

namespace B24\Project\History;

use B24\Project\Project;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Query;

class Service
{
    public function count(Filter $filter): int
    {
        return $this->createQuery()->where($filter->toCriteria())->queryCountTotal();
    }

    protected function createQuery(): Query
    {
        $query = HistoryTable::query();
        $query->setSelect([
            'ID',
            'PROJECT_ID',
            'AUTHOR_ID',
            'FIELD_NAME',
            'PREVIOUS_VALUE',
            'CURRENT_VALUE',
            'CHANGED_AT',
        ]);

        return $query;
    }

    public function getFragment(Filter $filter, array $order, int $pageSize, int $pageNumber): Collection
    {
        $offset = (max($pageNumber, 1) - 1) * $pageSize;
        $result = $this->createQuery()
            ->where($filter->toCriteria())
            ->setLimit($pageSize)
            ->setOffset($offset)
            ->setOrder($order)
            ->exec();

        $collection = new Collection();
        foreach ($result->fetchCollection() as $entityObject) {
            $collection->insert(new Entry(
                $entityObject->getId(),
                $entityObject->getProjectId(),
                $entityObject->getAuthorId(),
                $entityObject->getFieldName(),
                $entityObject->getPreviousValue(),
                $entityObject->getCurrentValue(),
                $entityObject->getChangedAt(),
            ));
        }

        return $collection;
    }

    public function register(Entry $entry): Entry
    {
        $entityObject = HistoryTable::createObject()
            ->setProjectId($entry->projectId)
            ->setAuthorId($entry->authorId)
            ->setFieldName($entry->fieldName)
            ->setPreviousValue($entry->previousValue ?? '')
            ->setCurrentValue($entry->currentValue ?? '')
            ->setChangedAt($entry->changedAt);

        $addResult = $entityObject->save();

        if (!$addResult->isSuccess()) {
            throw new ServiceException('Failed to add history entry.');
        }

        return $entry->withId($addResult->getId());
    }

    public function clearByProject(Project $project): void
    {
        $criteria = new ConditionTree();
        $criteria->where('PROJECT_ID', '=', $project->id);

        $result = $this->createQuery()->where($criteria)->exec();
        $collection = $result->fetchCollection();

        foreach ($collection as $item) {
            $deleteResult = $item->delete();

            if (!$deleteResult->isSuccess()) {
                throw new ServiceException('Failed to delete history entry.');
            }
        }
    }
}