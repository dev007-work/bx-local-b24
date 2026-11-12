<?php

namespace B24\Project;

use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;

class Service
{
    /**
     * @param Filter $filter
     * @return int
     * @throws ServiceException
     */
    public function count(Filter $filter): int
    {
        try
        {
            return ProjectTable::query()->where($filter->toCriteria())->queryCountTotal();
        }
        catch (SystemException $e)
        {
            throw new ServiceException('Failed to count projects', previous: $e);
        }
    }

    public function getFragment(Filter $filter, array $order, int $size, int $page): Collection
    {
        if ($size < 0) {
            throw new ServiceException('Page size MUST be a positive integer', 0);
        }

        $offset = (max($page, 1) - 1) * $size;

        return $this->findProjects($filter->toCriteria(), $order, $size, $offset);
    }

    /**
     * @param ConditionTree $criteria
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     * @throws ServiceException
     */
    private function findProjects(
        ConditionTree $criteria,
        array $order,
        ?int $limit = null,
        ?int $offset = null
    ): Collection
    {
        try
        {
            $result = ProjectTable::query()
                ->setSelect([
                    'ID',
                    'TITLE',
                    'CREATED_AT',
                    'CREATED_BY',
                    'UPDATED_AT',
                    'UPDATED_BY',
                    'ESTIMATED_COMPLETION_DATE',
                    'COMPLETION_DATE',
                    'DESCRIPTION',
                    'RESPONSIBLE_ID',
                    'COMMENT',
                    'INCOME',
                ])
                ->where($criteria)
                ->setOrder($order)
                ->setLimit($limit)
                ->setOffset($offset)
                ->exec();

            $projects = new Collection();
            foreach ($result->fetchCollection() as $entityObject) {
                $projects->insert(new Project(
                    $entityObject->getId(),
                    $entityObject->getTitle(),
                    $entityObject->getCreatedAt(),
                    $entityObject->getCreatedBy(),
                    $entityObject->getUpdatedAt(),
                    $entityObject->getUpdatedBy(),
                    $entityObject->getEstimatedCompletionDate() ?: null,
                    $entityObject->getCompletionDate() ?: null,
                    $entityObject->getDescription(),
                    $entityObject->getResponsibleId(),
                    $entityObject->getComment(),
                    $entityObject->getIncome(),
                ));
            }

            return $projects;
        }
        catch (SystemException $e)
        {
            throw new ServiceException('Failed to find project', previous: $e);
        }
    }

    public function create(Project $project): Project
    {

    }

    public function update(Project $project): void
    {

    }

    public function delete(Project $project): void
    {

    }

    public function getById(int $projectId): Project
    {

    }

    public function getByIds(int ...$projectIds): Collection
    {

    }
}